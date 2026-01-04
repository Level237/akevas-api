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
use App\Services\Payment\Coolpay\VerifyPayinService;
use App\Services\Payment\ValidatePaymentProductService;
use App\Services\Payment\Verify\HandleVerifyPaymentNotchpay;

class PaymentProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $userId;
    public $reference;
    /**
     * Create a new job instance.
     *
  
     */
    public function __construct($request, $userId, $reference)
    {
        // On ne stocke que des données simples (array), pas d'objet Request ou Closure
        $this->request = $request;
        $this->userId = $userId;
        $this->reference = $reference;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        $user = User::find($this->userId);

      

        $paymentStatus = (new VerifyPayinService())->verify($this->reference);
        $responseStatus = $paymentStatus->getData(true)['status'] ?? null;
        Log::info('PaymentProcessingJob: Payment processing for job', [
            "app" => $responseStatus,
        ]);
        if (!$user) {
            Log::error('PaymentProcessingJob: User not found', [
                "user_id" => $this->userId,
                "merchant_reference" => $this->reference,
            ]);
            return;
        }

        if (isset($responseStatus) && $responseStatus === "PENDING") {
            Log::info('PaymentProcessingJob: Payment processing for job', [
                "app" => $this->request,
                "reference" => $this->reference,
            ]);
            // On redéclenche le job avec les mêmes données, pas d'objet Request
            self::dispatch($this->request, $this->userId, $this->reference)->delay(now()->addSeconds(15));
        }

        if (isset($responseStatus) && $responseStatus === "FAILED" || $responseStatus === "CANCELED") {
            Log::error('PaymentProcessingJob: Payment failed');
             Log::info('PaymentProcessingJob: Payment processing for job', [
                "app" => $this->request,
                "reference" => $this->reference,
            ]);
            return;
        }

        if (isset($responseStatus) && $responseStatus == 'SUCCESS') {
            Log::info('PaymentProcessingJob: Payment complete');
            // On passe un objet Request reconstitué à la méthode handle
            (new ValidatePaymentProductService())->handle($this->request, $this->userId, $this->reference);
        }
    }
}
