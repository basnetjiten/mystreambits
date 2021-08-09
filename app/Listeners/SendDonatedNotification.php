<?php

namespace App\Listeners;

use App\Events\DonationProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDonatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DonationProcessed  $event
     * @return void
     */
    public function handle(DonationProcessed $event)
    {
        event($event);
    }
}
