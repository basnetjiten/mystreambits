<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EsewaPayController extends Controller
{
    public function esewaClientPayment(Request $request)
    {
        //dd($request->oid.$request->amt.$request->refId);
        if (isset($request->oid) && isset($request->amt) && isset($request->refId)) {
            //get the transaction associated with donors mobile number
            //whose token is unverified
            $transaction = Transaction::where('pid', $request->oid)
                ->where('token', 'unverified')
                ->latest()
                ->first();


            if ($transaction) {
                $url = "https://esewa.com.np/epay/transrec";
                $data = [
                    'amt' => $transaction->amount,
                    'rid' => $request->refId,
                    'pid' => $transaction->pid,
                    'scd' => Config::get('services.esewa.key')
                ];

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);


                $response_code = $this->get_xml_node_value('response_code', $response);
                //dd($response_code);
                if (trim($response_code) == 'Success') {
                    //update the transaction field with all the verified data from khalti server

                    $transaction->token = $request->refId;
                    $transaction->pay_type = "esewa";
                    $transaction->donation_status = "Completed";
                    $saved = $transaction->save();
                    //if successfully stored in our database
                    //dispatch the broadcast notification
                    if ($saved) {
                        ProcessDonation::dispatch($transaction,'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                        return redirect('/')->with('toast_success', 'payment successful');

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
