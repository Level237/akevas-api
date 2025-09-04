<?php

namespace App\Jobs;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Seller\NewMessageFromVerificationNotification;

class SendVerificationSellerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $shopId;
    protected $message;
    public function __construct($shopId,$message)
    {
        $this->shopId=$shopId;
        $this->message=$message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop=Shop::find($this->shopId);
        $seller=$shop->user;
        $seller->notify(new NewMessageFromVerificationNotification($this->message));
    }
}
