<?php

namespace App\Http\Controllers\Coins;

use App\Models\Shop;
use App\Models\User;
use NotchPay\Payment;
use NotchPay\NotchPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment as PaymentBackend;

class HandleWebhookController extends Controller
{
    public function handleWebhook(Request $request)
{
    NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
    $payload=$request->payload;
    $reference = $payload->data->reference;

    try {
        

        if ($request->status === 'failed') {
            $userId = explode('-', $reference)[0];
            $user = User::find($userId);

            if (!$user) return response()->json(['error' => 'User not found'], 404);

            // Vérifie si le paiement existe déjà
            if (!PaymentBackend::where('transaction_ref', $transaction->reference)->exists()) {
                PaymentBackend::create([
                    'payment_type' => 'coins',
                    'price' => $transaction->amount,
                    'transaction_ref' => $transaction->reference,
                    'payment_of' => 'coins',
                    'user_id' => $user->id,
                ]);

                $shop = Shop::where('user_id', $user->id)->first();
                $shop->coins += $transaction->amount;
                $shop->save();
            }
        }

        return response()->json(['message' => 'OK']);
    } catch (\Exception $e) {
        Log::error('Webhook NotchPay failed', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'server error'], 500);
    }
}
}
