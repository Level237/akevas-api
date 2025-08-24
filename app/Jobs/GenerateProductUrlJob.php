<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use App\Services\GenerateUrlResource;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateProductUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $productId;

    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
         $product = Product::find($this->productId);

          if (!$product) {
            return;
        }

        $baseSlug = (new GenerateUrlResource())->generateUrl($product->product_name);
        $uniqueSlug = $baseSlug;
        $counter = 1;

        if (Product::where('product_url', $baseSlug)->exists()) {
        while (Product::where('product_url', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$baseSlug}{$counter}";
            $counter++;
        }
    }
        $product->product_url = $uniqueSlug;
        $product->save();
    }
}
