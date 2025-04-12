<?php

namespace App\Http\Controllers\Coins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitPaymentController extends Controller
{
    public function initPaymentCoin(Request $request){

        try{
            $url = "https://api.notchpay.co/payments/initialize";
            $coins=$request->coins;
            $urlCallback=env("URL_FRONTEND")."/checkout/state?coins=$coins";
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "email"=>"brams@gmail.com",
                    "amount"=>"100",
                    "currency"=>"XAF",
                    "reference"=>"COINID".rand(123456, 999999),
                    "callback"=>$urlCallback,
                ]
                ),'application/json')->withHeaders([
                    "Authorization"=>"pk_test.aucN1k448sPoYBdotSJJ6U5IDXDFDFAhUbNSdVguuwHAuhBIGVC0NHTQgEl30m3Xtq83aqpSvq9rFA1VPi7cRUiaKr0fAh64xFLxIqiof4y7tZ6TuJ9FM4cUHs5Av"
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
