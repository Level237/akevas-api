<?php

namespace App\Listeners\Product;


use App\Events\Product\EventCheckSubscriptionProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ListenerCheckSubscriptionProduct
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
    public function handle(EventCheckSubscriptionProduct $event): void
    {
        $product=$event->product;
        $product->status=0;
        $product->isSubscribe=0;
        $product->expire=null;
        $product->save();
    }
}
