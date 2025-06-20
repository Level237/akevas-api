<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($request, $userId)
    {
        $this->request = $request;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reference = $this->request->reference ?? null;
        $amount = $this->request->amount ?? null;
        $merchant_reference = $this->request->merchant_reference ?? null;

        $user = User::find($this->userId);

        if (!$user) {
            Log::error('PaymentProcessingJob: User not found', [
                "user_id" => $this->userId,
                "merchant_reference" => $merchant_reference,
            ]);
            return;
        }

        if (!Payment::where('transaction_ref', $reference)->exists()) {
            Payment::create([
                'payment_type' => 'coins',
                'price' => $amount,
                'transaction_ref' => $reference,
                'payment_of' => 'coins',
                'user_id' => $user->id,
            ]);

            $shop = Shop::where('user_id', $user->id)->first();
            if ($shop) {
                $shop->coins += $amount;
                $shop->save();
                Log::info('PaymentProcessingJob: Coins added to shop', [
                    "user_id" => $user->id,
                    "shop_id" => $shop->id,
                    "amount" => $amount,
                    "reference" => $reference
                ]);
            } else {
                Log::warning('PaymentProcessingJob: Shop not found for user', [
                    "user_id" => $user->id,
                    "reference" => $reference
                ]);
            }
        } else {
            Log::info('PaymentProcessingJob: Payment already processed', [
                "reference" => $reference
            ]);
        }
    }
}
