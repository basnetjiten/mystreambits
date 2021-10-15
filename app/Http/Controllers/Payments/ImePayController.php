<?php

namespace App\Http\Controllers\Payments;

use App\Jobs\ProcessDonationMessage;
use App\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;

class ImePayController extends Controller
{
    public function imePayProcess(Request $request)
    {


        //dd($request->oid.$request->amt.$request->refId);
        /*$username = Config::get('services.ime.username');
        $password = Config::get('services.ime.pass');*/
        $username = "streamersalert";
        $password = " ime@12345";
        $userRequestPayment = $request->amount;
        $rand = rand(0, 99999);
        $uuid = str_pad($rand, 4, '0', STR_PAD_LEFT);

        $curl = curl_init();
        $data = array("MerchantCode" => "STREAMERS",
            "Amount" => $userRequestPayment,
            "RefId" => $uuid);
//https://stg.imepay.com.np:7979/api/Web/GetToken
        //https://payment.imepay.com.np:7979/api/Web/GetToken
        $postdata = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://stg.imepay.com.np:7979/api/Web/GetToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postdata,
            //STREAMERS
            //U1RSRUFNRVJT
            CURLOPT_HTTPHEADER => array(
                'Content-Type:application/json',
                'Authorization: Basic ' . base64_encode($username . ':' . $password),
                'Module: STREAMERS',
            )
        ));

        $response = curl_exec($curl);
        $decodedResponse = json_decode($response);
        dd($response);

        curl_close($curl);
        $imePayParams = $decodedResponse->TokenId . '|' . 'STREAMERS|' . $uuid . '|' . $userRequestPayment . '|' . 'GET|' . 'https://streamersalert.com/imePayCheckOut|' . 'https://streamersalert.com/imeCheckOutFailed';
        $imePayBase64Params = base64_encode($imePayParams);


        return response()->json([
            'data' => $imePayBase64Params,

        ]);


    }

    public function imeCheckOut(Request $request)
    {

        $query = $request->query('data');
        $decodedQuery = base64_decode($query);


        $splitData = explode('|', $decodedQuery, 7);
        //
        $responseCode = $splitData[0];

        $responseDes = $splitData[1];

        $msisdn = $splitData[2];

        $txnId = $splitData[3];

        $refId = $splitData[4];

        $txnAmount = $splitData[5];

        $tokenId = $splitData[6];


//dd($responseCode.$responseDes.$msisdn.$txnId.$refId.$txnAmount.$tokenId);
        //when opt is success and customer has sufficient balance
        if ($responseCode == "0") {

            $confirmPostData = array("MerchantCode" => "STREAMERS", "RefId" => $refId,
                "TokenId" => $tokenId,
                "TransactionId" => $txnId,
                "Msisdn" => $msisdn);
            $confirmPostJson = json_encode($confirmPostData);
            $username = Config::get('services.ime.username');
            $password = Config::get('services.ime.pass');
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://stg.imepay.com.np:7979/api/Web/Confirm',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $confirmPostJson,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/json',
                    'Authorization: Basic ' . base64_encode($username . ':' . $password),
                    'Module: U1RSRUFNRVJT',
                )
            ));

            $response = curl_exec($curl);

            $jsonConfirmResponse = json_decode($response);
            // dd($jsonConfirmResponse);
            // payment amount from client (of success payload)
            if ($jsonConfirmResponse->ResponseCode == "0" && $jsonConfirmResponse->ResponseDescription == 'Success') {

                //get the transaction associated with donors mobile number
                //whose token is unverified
                $transaction = Messages::where('phone', $jsonConfirmResponse->Msisdn)
                    ->where('token', 'unverified')
                    ->latest()
                    ->first();
                //update the transaction field with all the verified data from khalti server
                $transaction->amount = $txnAmount;
                $transaction->token = $tokenId;
                $transaction->pid = $txnId;
                $transaction->pay_type = "imepay";

                $transaction->donation_status = "Completed";
                $saved = $transaction->save();

                //if successfully stored in our database
                //dispatch the broadcast notification
                if ($saved) {
                    if ($transaction != null) {
                        ProcessDonationMessage::dispatch($transaction, 'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                        return response()->json(['success' => trans('donations.create.success')]);
                    }
                    return response()->json(['error' => trans('donations.create.error')]);
                    //ProcessDonation::dispatch($message,'liveAlert')->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                    //return redirect('/')->with('toast_success', 'payment successful');

                }
            } else abort('404');


            curl_close($curl);
            //echo $response;
        }


    }
}
