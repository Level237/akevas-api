<?php

namespace App\Http\Controllers\Coins;

use NotchPay\NotchPay;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use NotchPay\Payment as NotchPayPayment;

class HandleVerifyController extends Controller
{
    public function getPaymentStatus($ref)
    {
        
    
        // Cherche le paiement dans ta base
        $payment = Payment::where('transaction_ref', $ref)->first();
    
        if ($payment) {
            return response()->json([
                'status' => 'success',
                'coins' => $payment->price,
            ]);
        }
    
        // S’il n’existe pas dans ta base, tu peux vérifier via NotchPay directement (optionnel)
        try {
            $url = "https://api.notchpay.co/payments/".$ref;
                  
        $response=Http::acceptJson()->withHeaders([
                "Authorization"=>env("NOTCHPAY_API_KEY")
            ])->get($url);
            $responseData=json_decode($response);
            $transaction=$responseData->transaction;
            if ($transaction->status === 'failed' || $transaction->status === 'canceled') {
                return response()->json(['status' => 'failed']);
            }
    
            if ($transaction->status === 'pending') {
                return response()->json(['status' => 'pending']);
            }
    
            // Si c'est success mais qu'on n'a pas encore crédité ?
            if ($transaction->status === 'complete') {
                return response()->json(['status' => 'success']); // on attend encore le webhook
            }
            
            return response()->json(['status' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
}