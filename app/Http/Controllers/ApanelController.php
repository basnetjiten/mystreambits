<?php

namespace App\Http\Controllers;

use App\Invoices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Messages;
use App\Settings;
use App\User;
use App\PayoutSettings;
use App\Payouts;
use App\Configuration;

class ApanelController extends Controller
{

    var $view = [];

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Invoices
     */
    public function getInvoices()
    {
        $this->view['title'] = trans('invoice.invoice.title');
        return view('apanel.invoices', $this->view);
    }

    /**
     * Configurations
     */
    public function getConfigurations()
    {
        $configs = Configuration::orderBy('key', 'desc')->get();
        $this->view['data'] = [];
        foreach ($configs as $config)
            $this->view['data'][$config->key] = $config->value;

        $this->view['title'] = trans('apanel.configurations.title');
        return view('apanel.configurations', $this->view);
    }

    public function postConfigurations(Request $request)
    {

        $configurations = $request->all();
        foreach ($configurations as $key => $val) {
            if (base64_encode(base64_decode($key)) !== $key)
                continue;
            $key = base64_decode($key);
            $configuration = Configuration::where('key', $key)->first();
            if (!$configuration || $configuration->value == $val)
                continue;
            Configuration::where('key', $key)->update(['value' => $val]);
        }
        Configuration::reload();
        return response()->json(['success' => trans('apanel.configurations.success')]);
    }


    /**
     * Statistics
     */

    public function getStatistics()
    {

        /* Lat week messages */
        $messages = Messages::withTrashed()
            ->select('updated_at', 'amount', 'commission')
            ->where('status', 'success')
            ->where('updated_at', '>=', Carbon::now()->subWeek()->startOfDay())
            ->where('updated_at', '<=', Carbon::now())
            ->get();

        // Days
        for (
            $date = Carbon::now()->subWeek()->startOfDay()->setTimezone(Auth::user()->timezone);
            $date->lte(Carbon::now()->setTimezone(Auth::user()->timezone));
            $date->addDay()
        ) {
            $messageDates[] = $date->toDateString();
            $messageStatistics['amount'][$date->toDateString()] = 0;
            $messageStatistics['commission'][$date->toDateString()] = 0;
        }

        // Amount
        foreach ($messages as $message) {
            $date = with(new Carbon($message->updated_at))->setTimezone(Auth::user()->timezone)->toDateString();
            $messageStatistics['amount'][$date] += $message->amount;
            $messageStatistics['commission'][$date] += $message->commission;
        }

        $this->view['messageDates'] = &$messageDates;
        $this->view['messageStatistics'] = [
            'amount' => array_values($messageStatistics['amount']),
            'commission' => array_values($messageStatistics['commission'])
        ];

        /* Count messages */
        $this->view['counters']['paid_messages'] = Messages::where('status', 'success')->count();
        $this->view['counters']['messages'] = Messages::count();
        $this->view['counters']['amount'] = Messages::withTrashed()->where('status', 'success')->sum('amount');
        $this->view['counters']['commission'] = Messages::withTrashed()->where('status', 'success')->sum('commission');
        $this->view['counters']['refunds'] = Messages::withTrashed()->where('status', 'refund')->count();
        $this->view['counters']['amount_refunds'] = Messages::withTrashed()->where('status', 'refund')->sum('amount');
        $this->view['counters']['users'] = User::count();
        $this->view['counters']['today_users'] = User::where('created_at', '>=', Carbon::today())->count();

        $this->view['title'] = trans('apanel.statistics.title');
        return view('apanel.statistics', $this->view);
    }

    /**
     * Donations
     */
    public function getDonations()
    {
        $this->view['title'] = trans('apanel.donations.title');
        return view('apanel.donations', $this->view);
    }

    public function getDonationsData()
    {
        return DataTables::eloquent(Messages::select(['updated_at', 'user_id', 'name', 'amount', 'commission', 'message', 'id', 'status', 'billing_system'])->withTrashed()->whereIn('status', ['success', 'refund']))
            ->editColumn('updated_at', function ($data) {
                return $data->updated_at ? with(new Carbon($data->updated_at))->setTimezone(Auth::user()->timezone) : '';
            })
            ->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', '');
            })
            ->editColumn('commission', function ($data) {
                return number_format($data->commission, 2, '.', '');
            })
            ->editColumn('message', function ($data) {
                if (Auth::user()->smiles == 'true')
                    return Messages::smileys($data->message);
                else
                    return $data->message;
            })->editColumn('user_id', function ($data) {
                $user = User::where('id', $data->user_id)->first();
                return '<a href="' . route("apanel.users.edit", ["id" => $data->user_id]) . '">' . $user->name . '</a>';
            })->toJson();
    }

    /**
     * Users
     */
    public function getUsers()
    {
        $this->view['title'] = trans('apanel.users.title');
        return view('apanel.users', $this->view);
    }

    public function getUsersData()
    {
        try {
            return DataTables::eloquent(User::select(['id', 'name', 'stream_name',/*'balance',*/
                'email', 'timezone', 'avatar', 'token', 'created_at']))
                ->editColumn('created_at', function ($data) {
                    return $data->created_at ? with(new Carbon($data->created_at))->setTimezone(Auth::user()->timezone) : '';
                })->editColumn('stream_name', function ($data) {
                    return $data->stream_name;
                })/*->editColumn('balance', function ($data) {
                    return number_format($data->balance, 2, '.', '');
                })*/
                ->toJson();
        } catch (\Exception $e) {
        }
    }

    public function getUsersEdit(Request $request, $id)
    {
        $this->view['user'] = User::where('id', $id)->first();
        if (!$this->view['user'])
            abort(404);
        $this->view['title'] = trans('apanel.users.edit.title', ['id' => $this->view['user']->id]);
        $this->view['stream_name'] = trans('apanel.users.edit.stream_name', ['id' => $this->view['stream_name']->id]);
        return view('apanel.users_edit', $this->view);
    }

    public function postUsersEdit(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user)
            abort(404);

        $this->validate($request, [
            //'balance' => [ 'required', 'numeric', 'min:0', 'max:1000000' ],
            'name' => ['required', 'max:64'],
            'level' => ['required', 'in:admin,user'],
            'email' => ['nullable', 'email'],
            'timezone' => ['required', 'timezone'],
            'smiles' => ['required', 'in:true,false'],
            'links' => ['required', 'in:true,false'],
            'token' => ['required'],
        ]);

        $data = $request->only([/*'balance',*/
            'name', 'level', 'email', 'timezone', 'smiles', 'links', 'token', 'black_list_words']);

        $user->update($data);
        return response()->json(['success' => trans('settings.account.success')]);
    }

    public function getRequestedInvoices()
    {
        return DataTables::eloquent(Invoices::select(['id', 'user_id', 'updated_at', 'invoice_status', 'amount', 'commission_amount', 'invoice_id'])->whereIn('invoice_status', ['processing', 'ready']))
            ->editColumn('id', function ($data) {
                return $data->id;
            })->editColumn('updated_at', function ($data) {
                return ($data->updated_at->format('d M Y'));
            })->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', '');
            })->editColumn('commission_amount', function ($data) {
                return number_format($data->commission, 2, '.', '');
            })->editColumn('invoice_status', function ($data) {
                return $data->invoice_status;
            })->editColumn('invoice_id', function ($data) {
                return $data->invoice_id;
            })->editColumn('name', function ($data) {
                return User::find($data->user_id)->first()->name;
            })->toJson();

    }

    public function getPaidInvoices()
    {

        return DataTables::eloquent(Invoices::select(['user_id', 'updated_at', 'invoice_status', 'amount', 'commission_amount', 'invoice_id'])->whereIn('invoice_status', ['paid']))
            ->editColumn('updated_at', function ($data) {
                return ($data->updated_at->format('d M Y'));
            })->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', '');
            })->editColumn('commission_amount', function ($data) {
                return number_format($data->commission, 2, '.', '');
            })->editColumn('invoice_status', function ($data) {
                return $data->invoice_status;
            })->editColumn('invoice_id', function ($data) {
                return $data->invoice_id;
            })->editColumn('name', function ($data) {
                return User::find($data->user_id)->first()->name;
            })->toJson();

    }

    public function generateInvoice(Request $request)
    {

        $this->validate($request, [
            'id' => ['required', 'integer'],
        ]);

        $invoice = Invoices::where(['invoice_id' => $request->invoice_id])->first();
        $user = User::find($invoice->user_id);
        $image = base64_encode(file_get_contents(public_path('/img/logoo.jpg')));

        $this->view['invoice'] = [
            'img' => array_values($image),
            'username' => array_values($user->name),
            'email' => array_values($user->email),
            'commission' => array_values($invoice->commission),
            'amount' => array_values($invoice->amount),
            'payments' => array_values($user->payment),
            'id' => array_values($invoice->invoice_id),
        ];


        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('paid_invoice', $this->view['invoice']);
        $pdf->setPaper('A5', 'portrait');
        return $pdf->download($user->name . 'pdf');
    }

    public function postUpdateInvoice(Request $request)
    {


        $this->validate($request, [
            'invoice_id' => ['required', 'integer'],
        ]);


        $invoice = Invoices::where('invoice_id', $request->invoice_id)->whereIn('invoice_status', ['processing'])->first();

        if ($invoice != null) {

            $invoice->invoice_status = 'ready';


        } else {
            $invoice = Invoices::where('invoice_id', $request->invoice_id)->whereIn('invoice_status', ['ready'])->first();
            $invoice->invoice_status = 'processing';


        }
        $invoice->save();
        if ($invoice)
            return response()->json(['success' => trans('success')]);
        return response()->json(['fail' => trans('failed')]);

    }


}