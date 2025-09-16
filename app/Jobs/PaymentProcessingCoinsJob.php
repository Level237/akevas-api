<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Payment\Coolpay\VerifyPayinService;
use App\Services\Payment\ValidatePaymentCoinService;

class PaymentProcessingCoinsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $amount;
    public $reference;

    public function __construct($amount,$reference)
    {
        $this->amount=$amount;
        $this->reference=$this->reference;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId=Auth::guard('api')->user()->id;

        $paymentStatus = (new VerifyPayinService())->verify($reference);
            $responseStatus = $paymentStatus->getData(true)['status'] ?? null;
            if (isset($responseStatus) && $responseStatus === "PENDING") {
                    
                // On redéclenche le job avec les mêmes données, pas d'objet Request
                Self::dispatch($amount, $reference)->delay(now()->addSeconds(15));
            }
            if (isset($responseStatus) && $responseStatus === "FAILED" || $responseStatus === "CANCELED") {
            
                
                return;
            }

            if (isset($responseStatus) && $responseStatus == 'SUCCESS') {
                (new ValidatePaymentCoinService())->handle($this->reference,$this->amount,$userId);
            }
        
    }
}
