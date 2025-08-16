<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Seller\NewOrderFromSellerNotification;

class SendNewOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *  /**
     * L'ID de la commande
     *
     * @var int
     */
    protected $orderId;

    /**
     * L'ID du produit
     *
     * @var string
     */
    protected $productId;

    /**
     * Crée une nouvelle instance de job.
     *
     * @param int $orderId
     * @param string $productId
     * @return void
     
     */
    public function __construct($orderId,$productId)
    {
         $this->orderId = $orderId;
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);
        $product = Product::find($this->productId);

        if ($order && $product && $product->shop && $product->shop->user) {
            $shop = $product->shop;
            $seller = $shop->user; // Accès au vendeur via la boutique

            $seller->notify(new NewOrderFromSellerNotification($order));
        }
    }
}
