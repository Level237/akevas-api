<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\User;
use NotchPay\NotchPay;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Payment\Verify\HandleVerifyPaymentNotchpay;

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
        
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        $user = User::find($this->userId);

        $paymentStatus=(new HandleVerifyPaymentNotchpay())->verify($this->request->reference);
        $responseStatus=$paymentStatus->getData(true)['status'];
        if (!$user) {
            Log::error('PaymentProcessingJob: User not found', [
                "user_id" => $this->userId,
                "merchant_reference" => $this->request->reference,
            ]);
            return;
        }

        if(isset($responseStatus) && $responseStatus === "processing"){
            Self::dispatch($this->request,$this->userId)->delay(now()->addSeconds(5));
        }

        if (isset($responseStatus) && $responseStatus == 'complete') {
        
        $processPaymentService=(new ValidatePaymentProductService())->handle($this->request,$this->userId);
    }
    }
}
