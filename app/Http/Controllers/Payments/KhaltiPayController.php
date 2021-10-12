<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class KhaltiPayController extends Controller
{
    //performs client payment verification obtained from khalti payload
    public function khaltiClientPayment(Request $request)
    {
        //argument obtained from khalti success payload
        $args = http_build_query(array(
            'token' => $request->token,
            'amount' => $request['amount']
        ));

        $url = "https://khalti.com/api/v2/payment/verify/";

        # Make the call using API for payment verification using our secret
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //test_secret_key_70b1a031600a45debf6fa5ed858165b7
        $khaltiSecretKey = Config::get('services.khalti.key');
        $headers = ["Authorization: {$khaltiSecretKey}"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //decode the successResponse
        $khaltiResponse = json_decode($response);

        //check if verified payment from khalti server matches
        // payment amount from client (of success payload)
        if ($khaltiResponse->amount == $request['amount']) {

            //get the transaction associated with donors mobile number
            //whose token is unverified
            $transaction = Transaction::where('phone', $khaltiResponse->user->mobile)
                ->where('token', 'unverified')
                ->latest()
                ->first();
            //update the transaction field with all the verified data from khalti server
            $transaction->amount = $khaltiResponse->amount / 100;
            $transaction->token = $request->token;
            $transaction->pay_type = "khalti";
            $transaction->message = $request['message'];
            $transaction->donation_status = $khaltiResponse->state->name;
            $saved = $transaction->save();

            //if successfully stored in our database
            //dispatch the broadcast notification
            if ($saved) {
                ProcessDonation::dispatch($transaction)->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
            }
        } else abort('404');

        curl_close($ch);


    }
}
