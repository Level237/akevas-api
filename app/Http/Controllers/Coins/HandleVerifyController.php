<?php

namespace App\Http\Controllers\Coins;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
            $transaction = NotchPayPayment::verify($ref)->transaction;
    
            if ($transaction->status === 'failed' || $transaction->status === 'cancelled') {
                return response()->json(['status' => 'failed']);
            }
    
            if ($transaction->status === 'pending') {
                return response()->json(['status' => 'pending']);
            }
    
            // Si c'est success mais qu'on n'a pas encore crédité ?
            if ($transaction->status === 'success') {
                return response()->json(['status' => 'processing']); // on attend encore le webhook
            }
    
            return response()->json(['status' => 'unknown']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
}
