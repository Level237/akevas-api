<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Shop\GetTotalEarningService;

class GetShopEarningsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     public $productId;
    public function __construct($productId)
    {
        $this->productId=$productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product=Product::findOrFail($this->productId);
        $shopId=$product->shop_id;
        $totalEarnings=(new GetTotalEarningService())->getTotalEarning($shopId);
        $shop=Shop::findOrFail($shopId);
        $shop->total_earning=$totalEarnings;
        $shop->save();
    }
}
