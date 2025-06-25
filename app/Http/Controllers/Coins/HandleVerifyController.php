<?php

namespace App\Http\Controllers\Coins;

use NotchPay\NotchPay;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use NotchPay\Payment as NotchPayPayment;

class HandleVerifyController extends Controller
{
    public function getPaymentStatus(Request $request)
    {
        
    
        // Cherche le paiement dans ta base
        
    
        // S’il n’existe pas dans ta base, tu peux vérifier via NotchPay directement (optionnel)
        try {

            if(isset($request->reference)){
                $url = "https://api.notchpay.co/payments/".$request->reference;
                  
                $response=Http::acceptJson()->withHeaders([
                        "Authorization"=>env("NOTCHPAY_API_KEY")
                    ])->get($url);
                    Log::info('ref',[
                        'reference'=>$request->reference
                    ]);
                    $responseData=json_decode($response);
                    $transaction=$responseData->transaction;

                    Log::info('ref',[
                        'reference'=>$transaction
                    ]);
                    if ($transaction->status === 'failed' || $transaction->status === 'canceled' || $transaction->status==="abandoned") {
                        return response()->json(['status' => 'failed']);
                    }
        
                    
            
                    if ($transaction->status === 'pending') {
                        return response()->json(['status' => 'pending']);
                    }
            
                    // Si c'est success mais qu'on n'a pas encore crédité ?
                    if ($transaction->status === 'complete') {
                        return response()->json(['status' => 'complete']); // on attend encore le webhook
                    }if($transaction->status === 'processing'){
                        return response()->json(['status' => 'processing']);
                    }
                    
                    return response()->json(['status' => $transaction]);
            }
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
}