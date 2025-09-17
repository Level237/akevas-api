<?php

namespace App\Services\Payment\Coolpay;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class VerifyPayinService{

    public function verify($transaction_ref){

        $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY")."/checkStatus/".$transaction_ref;

        $response=Http::get($url);
        $responseData=json_decode($response);

        return response()->json(['status' => $responseData->transaction_status]);
    }

    public function verifyCoins($transaction_ref){
        $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY_COINS")."/checkStatus/".$transaction_ref;

        $response=Http::get($url);
        $responseData=json_decode($response);
        Log::info('level', [
            "amount" => $transaction_ref,
        ]);
        return response()->json(['status' => $responseData->transaction_status]);
    }
}