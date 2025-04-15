<?php

namespace App\Http\Controllers\Coins;

use NotchPay\Payment;
use NotchPay\NotchPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function PaymentCoin(Request $request){

        $response=$this->initPayment($request);
        return response()->json($response);
    }

    private function initPayment(Request $request){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

            $urlCallback="https://dev.akevas.com";
                
           
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>$request->coins,
                "currency"=>"XAF",
                "reference"=>Auth::guard('api')->user()->id . '-' . uniqid(),
                "callback"=>$urlCallback,
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>"pk.I0lJZM2oyHXyDdJusvjfgRjiIA5yPaiRxUivIExtPDyA7Buh4TBHObTW9HMfrdb7L5V9wzzNoJwMNDU9ZTUnn6sDB1rRPf1jgbBrQkeptdx305neBmOEYoHHmt9x1"
            ])->post($url);

            return response->transaction->reference;
    
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
    private function charge($reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{
            $url = "https://api.notchpay.co/payments/".$reference;
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "channel" => "cm.orange",
                    "data" => [
                        "phone" => "+237690394365"
                    ]
                    ],
                ),'application/json')->withHeaders([
                    "Authorization"=>"pk.I0lJZM2oyHXyDdJusvjfgRjiIA5yPaiRxUivIExtPDyA7Buh4TBHObTW9HMfrdb7L5V9wzzNoJwMNDU9ZTUnn6sDB1rRPf1jgbBrQkeptdx305neBmOEYoHHmt9x1"
                ])->post($url);
    
                return response->transaction->reference;
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
