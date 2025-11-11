<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\NewMessageFromVerificationProductNotification;

class SendDeclineProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $product;
    protected $message;
    public function __construct($product,$message)
    {
        $this->product=$product;
        $this->message=$message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop=$this->product->shop;
        $seller=$shop->user;
        $seller->notify(new NewMessageFromVerificationProductNotification($this->message,$this->product));
    }
}
