<?php

namespace App\Http\Controllers\Payments;

use App\Jobs\ProcessDonationMessage;
use App\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;

class KhaltiPayController extends Controller
{

    private function _commission($amount)
    {
        return round(($amount / 100) * config('khalti.commission'), 2);
    }

    //performs client payment verification obtained from khalti payload
    public function khaltiVerification(Request $request)
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
        //test_public_key_dc74e0fd57cb46cd93832aee0a390234
        $khaltiSecretKey = "test_public_key_dc74e0fd57cb46cd93832aee0a390234"; /*Config::get('services.khalti.key');*/
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
            $message = Messages::where('donator_id', $request->donator_id)
                ->where('invoice_status', 'unpaid')
                ->latest()
                ->first();
            //update the transaction field with all the verified data from khalti server
            $message->amount = $khaltiResponse->amount / 100;
            $message->token = $request->token;
            $message->billing_system = "khalti";
            $message->status = 'success';
            $message->commission = $this->_commission($message->amount);

            $saved = $message->save();

            //if successfully stored in our database
            //dispatch the broadcast notification
            if ($saved) {
                if ($message != null) {
                    ProcessDonationMessage::dispatch($message->user_id)->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                    return response()->json(['success' => trans('donations.create.success')]);
                }
                return response()->json(['error' => trans('donations.create.error')]);
                //ProcessDonation::dispatch($message,'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                //return redirect('/')->with('toast_success', 'payment successful');

            }
        } else abort('404');

        curl_close($ch);


    }
}
