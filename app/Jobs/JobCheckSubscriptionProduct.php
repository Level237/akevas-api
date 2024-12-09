<?php

namespace App\Jobs;

use App\Events\Product\EventCheckSubscriptionProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCheckSubscriptionProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = Carbon::now();

        $expiredProducts = Product::where('expire', '<', $today)->get();

        foreach($expiredProducts as $product){
            event(new EventCheckSubscriptionProduct($product));
        }
    }
}
