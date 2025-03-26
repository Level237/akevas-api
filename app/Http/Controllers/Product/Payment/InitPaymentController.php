<?php

namespace App\Http\Controllers\Product\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function initPayment(Request $request){
        try{
            $url = "https://api.notchpay.co/payments/initialize";
            $source=$request->s;
            if($source=="0"){
            $total = $request->total;
            $shipping = $request->shipping;
            $productId = $request->productId;
            $quantity = $request->quantity;
            $name = urlencode($request->name);
            $price = $request->price;
            $quarter = urlencode($request->quarter);
            $address = urlencode($request->address);
            $paymentMethod = $request->paymentMethod;
            $urlCallback="http://localhost:5173/checkout/state?method=$paymentMethod&source=$source&total=$total&shipping=$shipping&productId=$productId&quantity=$quantity&price=$price&quarter=$quarter&name=$name&address=$address";
                
            }else{
                
                $urlCallback="http://localhost:5173/checkout/state?source=$source";
            }
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>"brams@gmail.com",
                "amount"=>"100",
                "currency"=>"XAF",
                "reference"=>"REFID".rand(123456, 999999),
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
