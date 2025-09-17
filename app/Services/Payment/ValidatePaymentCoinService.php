<?php

namespace App\Services\Payment;

use App\Models\Shop;
use App\Models\Payment;


class ValidatePaymentCoinService
{

    public function handle(
        $reference,
        $amount,
        $userId
        ){

            if (!Payment::where('transaction_ref', $reference)->exists()) {
                Payment::create([
                    'payment_type' => 'coins',
                    'price' => $amount,
                    'transaction_ref' => $reference,
                    'payment_of' => 'coins',
                    'user_id' => $userId,
                ]);
               
                    $shop = Shop::where('user_id', $userId)->first();
                    $shop->coins += $amount;
                    $shop->save();
                
                
            }
        }
}