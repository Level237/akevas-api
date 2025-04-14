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
    public function initPaymentCoin(Request $request){

        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

            $urlCallback="https://c996-129-0-76-158.ngrok-free.app/api/callback/payment";
                
           
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>$request->coins,
                "currency"=>"XAF",
                "reference"=>Auth::guard('api')->user()->id . '-' . uniqid(),
                "callback"=>$urlCallback,
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>"pk_test.5DVUNSzBbBAts5Y0FxelUrNeeT1hvlY9kvwkWVL7Ck6hO5CCQkbXHrzUZ4cpWnCQlvxPSrlB5LztJwRdFTZQ2QNbGJQPTeEhFz3x5sxf3SK2V62jgX7RlHfZXdbhK"
            ])->post($url);

            return json_decode($response);
    
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
