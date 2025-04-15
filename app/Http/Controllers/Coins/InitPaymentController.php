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
        $reference=Auth::guard('api')->user()->id . '-' . uniqid();
        $response=$this->initPayment($request,$reference);
        $responseCharge=$this->charge($reference);
        return response()->json([
            "status"=>"success",
            "message"=>"Payment initiated",
            "reference"=>$reference,
            "statusCharge"=>$responseCharge
        ]);
    }

    private function initPayment(Request $request,$reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

            $urlCallback="http://localhost:5173/coins/confirmation";
                
           
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>$request->coins,
                "currency"=>"XAF",
                "reference"=>$reference,
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
    
                return response->status;
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
