<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
    public $userId;

    public function __construct($amount,$userId,$reference)
    {
        $this->amount=$amount;
        $this->reference=$reference;
        $this->userId=$userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        Log::info("martin",[
            'ref'=>$this->reference
        ]);
        $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY_COINS")."/checkStatus/".$this->reference;

        $response=Http::get($url);
        $responseData=json_decode($response);
       
            $responseStatus = $responseData->transaction_status;
            Log::info('PaymentProcessingCoinsJob: Payment processing for job', [
                "amount" => $responseStatus,
            ]);
            if (isset($responseStatus) && $responseStatus === "PENDING") {
                    
                Log::info('PaymentProcessingCoinsJob: Payment processing for job', [
                    "amount" => $this->amount,
                    "reference" => $this->reference
                ]);
                // On redéclenche le job avec les mêmes données, pas d'objet Request
                Self::dispatch($this->amount,$this->userId, $this->reference)->delay(now()->addSeconds(15));
            }
            if (isset($responseStatus) && $responseStatus === "FAILED" || $responseStatus === "CANCELED") {
            
                
                return;
            }

            if (isset($responseStatus) && $responseStatus == 'SUCCESS') {
                (new ValidatePaymentCoinService())->handle($this->reference,$this->amount,$this->userId);
            }
        
    }
}
