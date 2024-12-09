<?php

namespace App\Listeners;

use App\Events\MakePaymentEvent;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MakePaymentListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MakePaymentEvent $event)
    {
        $payment=Payment::create($event->data);

        return $payment;
    }
}
