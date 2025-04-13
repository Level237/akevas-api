<?php

namespace App\Http\Controllers\Coins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function initPaymentCoin(Request $request){

        try{
            $url = "https://api.notchpay.co/payments/initialize";
            $coins=$request->coins;
            $urlCallback="http://localhost:5173/checkout/state?coins=$coins";
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "email"=>"brams23@gmail.com",
                    "amount"=>"100",
                    "currency"=>"XAF",
                    "reference"=>"COINID".rand(123456, 999999),
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
