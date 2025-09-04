<?php

namespace App\Jobs;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Shop\generateShopNameService;

class GenerateUniqueShopKeyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $shopId;
    public function __construct($shopId)
    {
        $this->shopId=$shopId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop=Shop::find($this->shopId);
        $shop->shop_key=(new generateShopNameService)->generateUniqueShopName($shop->shop_name);
        $shop->save();
    }
}
