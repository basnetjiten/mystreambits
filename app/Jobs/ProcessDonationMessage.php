<?php

namespace App\Jobs;

use App\Events\AlertDonationBroadCastEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDonationMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $donationMessage;

    /**
     * Create the event listener.
     *
     * @param $userDonationMessage
     */
    public function __construct($userDonationMessage)
    {
        $this->donationMessage = $userDonationMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        event(new AlertDonationBroadCastEvent($this->donationMessage));


    }
}
