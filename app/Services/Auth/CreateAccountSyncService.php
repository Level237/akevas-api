<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;

class CreateAccountSyncService {

    public function createSyncAccount($sellerName,$url,$email,$phone,$sellerId){

        $response=Http::acceptJson()->withHeaders([ "Authorization"=>env("NOTCHPAY_API_KEY"),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('https://api.notchpay.co/accounts', [
        'type' => 'standard', // ou 'express', 'custom'
        'business_profile' => [
            'name' => $sellerName,
            'url' => $url,
            'category' => "none",
        ],
        'email' => $email,
        'phone' => $phone,
        'metadata' => [
            'seller_id' => $sellerId,
            'internal_reference' => "Seller_".$sellerId,
        ],
    ]);

    $responseData=json_decode($response);

    return $responseData->account->id;

    }
}