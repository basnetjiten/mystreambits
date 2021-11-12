<?php

namespace App\Http\Controllers\Payments;

use App\Jobs\ProcessDonationMessage;
use App\Messages;
use Illuminate\Http\Request;
use Nimbbl\Api\NimbblApi;
use App\Http\Controllers\Controller;

class PayUController extends Controller
{

    private function _commission($amount)
    {
        return round(($amount / 100) * config('payu.commission'), 2);
    }

    //performs client payment verification obtained from payu payload
    public function payUOrderProcess(Request $request)
    {


        $api = new NimbblApi('access_key_mZR7lyzeQw6Lbv9p', 'access_secret_BrQv91z6AE28Bvzg');
        $order_data = array(
            'referrer_platform' => 'referrer_platform',
            'merchant_shopfront_domain' => 'http://example.com',
            'invoice_id' => $request->donatorId,
            'order_date' => date('Y-m-d H:i:s'),
            'currency' => 'INR',
            'amount_before_tax' => $request->amount,
            'tax' => 0,
            'total_amount' => $request->amount,
            'description' => 'This is a test order...',
        );
        $newOrder = $api->order->create($order_data);


    }

    public function verifyPayment(Request $request)
    {

        $nimbblSignature = $request->nimbblSignature;
        $orderId = $request->orderId;
        $transactionId = $request->transactionId;
        $secreteKey = "access_secret_BrQv91z6AE28Bvzg";

        $generated_signature = hmac_sha256($orderId . "|" . $transactionId, $secreteKey);

        if ($generated_signature == $nimbblSignature) {

            $message = Messages::where('donator_id', $request->donatorId)
                ->where('invoice_status', 'unpaid')
                ->latest()
                ->first();
            $message->token = $orderId;
            $message->biling_system = "payu";
            $message->status = "success";
            $message->commission = $this->_commission($message->amount);
            $saved = $request->save();
            //if successfully stored in our database
            //dispatch the broadcast notification
            if ($saved) {
                if ($message != null) {
                    ProcessDonationMessage::dispatch($message->user_id)->onConnection(env('QUEUE_CONNECTION'))->onQueue(env('SQS_QUEUE'))->delay(now()->addSecond(30));
                    return response()->json(['success' => trans('donations.create.success')]);
                }
                return response()->json(['error' => trans('donations.create.error')]);


            }
        }

    }
}
