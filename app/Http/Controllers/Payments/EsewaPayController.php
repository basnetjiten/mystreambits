<?php

namespace App\Http\Controllers\Payments;

use App\Jobs\ProcessDonationMessage;
use App\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;


class EsewaPayController extends Controller
{
    /* public function __construct()
     {
         config('paypal.sandbox.certificate', false);
         config('paypal.live.certificate', false);
     }*/

    private function _commission($amount)
    {
        return round(($amount / 100) * config('paypal.commission'), 2);
    }

    public function esewaPaymentInitiator()
    {
       // $message = Messages::where('id', $id)->first();


        $url = "https://uat.esewa.com.np/epay/main";
        $data = [
            'amt' => 100,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => 100,
            'pid' => 'ee2c3ca1-696b-4cc5-a6be-2c40d929d453',
            /*'amt' => $message->amount,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => $message->amount,
            'pid' => $message->donator_id,*/
            'scd' => 'EPAYTEST',
            /*'su' => "http://127.0.0.1:8000/esuccess?q=su",
            'fu' => "http://127.0.0.1:8000/efailure?q=su"*/
            'su'=>'http://127.0.0.1:8000/pages.contact?q=su',
            'fu'=>'http://127.0.0.1:8000/pages.contact?q=fu'
        ];


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        //$httpReturnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //dd($httpReturnCode);

        //curl_close($curl);
        //dd($response);
    }


    public function esewaSuccessPayment(Request $request)
    {
        //dd($request->oid.$request->amt.$request->refId);
        if (isset($request->oid) && isset($request->amt) && isset($request->refId)) {
            //get the transaction associated with donors mobile number
            //whose token is unverified

            $message = Messages::where('donator_id', $request->oid)
                ->where('invoice_status', 'unpaid')
                ->latest()
                ->first();

           // https://uat.esewa.com.np/epay/transrec
            //https://esewa.com.np/epay/transrec
            if ($message) {
                $url = "https://uat.esewa.com.np/epay/transrec";
                $data = [
                    'amt' => $message->amount,
                    'rid' => $request->refId,
                    'pid' => $request->donator_id,
                    'scd' => 'EPAYTEST'/*Config::get('services.esewa.key')*/
                ];

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);


                $response_code = $this->get_xml_node_value('response_code', $response);
                dd($response_code);
                if (trim($response_code) == 'Success') {
                    //update the transaction field with all the verified data from khalti server

                    $message->token = $request->refId;
                    $message->biling_system = "esewa";
                    $message->status = "success";
                    $saved = $request->save();
                    //if successfully stored in our database
                    //dispatch the broadcast notification
                    if ($saved) {
                        if ($message != null) {
                            ProcessDonationMessage::dispatch($message, 'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                            return response()->json(['success' => trans('donations.create.success')]);
                        }
                        return response()->json(['error' => trans('donations.create.error')]);
                        //ProcessDonation::dispatch($message,'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                        //return redirect('/')->with('toast_success', 'payment successful');

                    }
                }

            }


        } else abort('404');
    }

    public function get_xml_node_value($node, $xml)
    {
        if ($xml == false) {
            return false;
        }
        $found = preg_match('#<' . $node . '(?:\s+[^>]+)?>(.*?)' .
            '</' . $node . '>#s', $xml, $matches);
        if ($found != false) {

            return $matches[1];

        }

        return false;
    }

    public function eFail()
    {
        dd("Failure");
    }
}
