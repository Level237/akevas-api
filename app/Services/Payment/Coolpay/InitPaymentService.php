<?php

namespace App\Services\Payment\Coolpay;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class InitPaymentService{

    public function initPay(
        $price,
        $reason,
        $transaction_id,
        $key_public
    ){

        $response=Http::acceptJson()->withBody(
            json_encode(
                [
                    "transaction_amount"=>$price,
                    "transaction_currency"=>"XAF",
                    "transaction_reason"=>$reason,
                    "app_transaction_ref"=>$transaction_id,
                    "customer_phone_number"=>Auth::guard('api')->user()->phone_number,
                    "customer_name"=>Auth::guard('api')->user()->username,
                    "customer_email"=>Auth::guard('api')->user()->email,
                    "customer_lang"=>"fr",
                  ]),'application/json')->post("https://my-coolpay.com/api/".$key_public."/paylink",[

        ]);

        return json_decode($response);
    }
}
