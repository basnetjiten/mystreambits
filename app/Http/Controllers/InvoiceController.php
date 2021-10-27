<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Messages;
use App\Invoices;
use PDF;


class InvoiceController extends Controller
{

    var $view = [];

    public function __construct()
    {
        //
    }

    public function getInvoices()
    {
        $this->view['title'] = trans('invoice.invoice.title');
        return view('invoice.invoice', $this->view);
    }

    public function raiseInvoice(Request $request)
    {
        if (Messages::where('user_id', Auth::id())->first() != null) {


            $messages = Messages::where(['user_id' => Auth::id(), 'invoice_status' => 'unpaid', 'status' => 'success'])->get();


            $totalUnpaidAmount = $messages->sum('amount');
            $totalCommissionAmount = $messages->sum('commission');

            $invoice = Invoices::where(['user_id' => Auth::id(), 'invoice_status' => ('processing')])->first();
            if ($invoice) {
                $invoice->update(['user_id' => Auth::id(),
                    'amount' => $totalUnpaidAmount,
                    'commission_amount' => $totalCommissionAmount]);

            } else {
                //Store Unique Donator/Invoice Number
                $unique_no = Messages::orderBy('id', 'DESC')->pluck('id')->first();
                if ($unique_no == null or $unique_no == "") {
                    #If Table is Empty
                    $unique_no = 963;
                } else {
                    #If Table has Already some Data
                    $unique_no = $unique_no + 3;
                }
                $newInvoice = Invoices::create([
                    'user_id' => Auth::id(),
                    'amount' => $totalUnpaidAmount,
                    'commission_amount' => $totalCommissionAmount,
                    'invoice_id' => $unique_no
                ]);
                if ($newInvoice) {
                    $newInvoice->save();
                    return response()->json(['success' => trans('invoice.create.success')]);

                }
                return response()->json(['error' => trans('invoice.create.error')]);

            }

        }


    }

    public function getData()
    {
        return DataTables::eloquent(Invoices::select(['updated_at', 'invoice_status', 'amount', 'commission_amount', 'invoice_id'])->where('user_id', Auth::id()))
            ->editColumn('updated_at', function ($data) {
                return $data->updated_at ? with(new Carbon($data->updated_at))->setTimezone(Auth::user()->timezone) : '';
            })->editColumn('amount', function ($data) {
                return number_format($data->amount, 2, '.', '');
            })->editColumn('commission_amount', function ($data) {
                return number_format($data->commission, 2, '.', '');
            })->editColumn('invoice_status', function ($data) {
                return $data->invoice_status;
            })->editColumn('invoice_id', function ($data) {
                return $data->invoice_id;
            })->toJson();
    }


}