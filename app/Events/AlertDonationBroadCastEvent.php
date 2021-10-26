<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertDonationBroadCastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $donationMessageToken;

    /**
     * Create a new event instance.
     *
     * @param $userDonatedMessage
     */
    public function __construct($userDonatedMessage)
    {

        $this->donationMessageToken = $userDonatedMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // dd( $this->currentTransByDonorForStreamer.$this->currentTransByDonorForStreamer->streamer->id.$this->currentTransByDonorForStreamer->donor->name);

        return new PrivateChannel('op-stream-bits' . $this->donationMessageToken);
    }


    /*public function broadcastWith()
    {
        return [
            'donation' => [
                'amount' => $this->donationMessage->amount,
                'message' => $this->donationMessage->message,
                'source' => $this->donationMessage->biling_system,

            ],

            'donor' => [
                'name' => $this->donationMessage->name
            ],

        ];
    }*/
}
