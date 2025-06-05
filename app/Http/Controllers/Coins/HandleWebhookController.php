<?php

namespace App\Http\Controllers\Coins;

use App\Models\Shop;
use App\Models\User;
use NotchPay\NotchPay;
use App\Models\Payment;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Payment\Verify\HandleVerifyPaymentNotchpay;


class HandleWebhookController extends Controller
{
    public function handleWebhook(Request $request)
{
    NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
    $payload=$request->data;
    $reference = $payload['reference'];
    $merchant_reference=$payload['merchant_reference'];
    $amount=$payload['amount'];
   
    try {
        
        $paymentStatus=(new HandleVerifyPaymentNotchpay())->verify($reference);
        $responseStatus=$paymentStatus->getData(true)['status'];
       
        if (isset($responseStatus) && $responseStatus == 'complete') {
            $userId = explode('-', $merchant_reference)[0];
            $user = User::find($userId);
            
              
            if (!$user) return response()->json(['error' => 'User not found'], 404);

            // Vérifie si le paiement existe déjà
            if (!Payment::where('transaction_ref', $reference)->exists()) {
                Payment::create([
                    'payment_type' => 'coins',
                    'price' => $amount,
                    'transaction_ref' => $reference,
                    'payment_of' => 'coins',
                    'user_id' => $user->id,
                ]);
                
                $shop = Shop::where('user_id', $user->id)->first();
                $shop->coins += $amount;
                $shop->save();
                Log::info('Payment failed for user', [
                                "user"=>$user,
                                "type"=>$responseStatus,
                                'amount' => $amount
                            ]);
            }
        }

        return response()->json(['message' => 'OK']);
    } catch (\Exception $e) {
        Log::error('Webhook NotchPay failed', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'server error'], 500);
    }
}
}
