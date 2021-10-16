<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\Invoices;
use Carbon\Carbon;
use App\Messages;
use Illuminate\Support\Facades\Request;

class InvoiceController extends Controller
{

    var $view = [];

    public function __construct()
    {
        //
    }

    public function index()
    {
        return view('home.landing');
    }


    public function raiseInvoice(Request $request)
    {
        $invoice = Invoices::where('user_id', Auth::id())->first();
        if ($invoice) {
            Invoices::where($request->user_id)->update($request->all());

        } else {
            $totalUnpaidAmount = Messages::whereIn('user_id', Auth::id())->where(['invoice_status' => 'unpaid', 'status' => 'success',])->sum('amount');
            $totalCommissionAmount = Messages::whereIn('user_id', Auth::id())->where(['invoice_status' => 'unpaid', 'status' => 'success',])->sum('commission');

            $newInvoice = Invoices::create([
                'user_id' => Auth::id(),
                'amount' => $totalUnpaidAmount,
                'commission_amount' => $totalCommissionAmount,
            ]);
            if ($newInvoice) {
                $newInvoice->save();
            }

        }

        $this->view['title'] = trans('invoice.invoice.title');
        return view('home.dashboard', $this->view);


    }

}