<?php

namespace App\Http\Controllers\Payments;

use App\Jobs\ProcessDonationMessage;
use App\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;


class MoruPayController extends Controller
{


    private function _commission($amount)
    {
        return round(($amount / 100) * config('moru.commission'), 2);
    }


}
