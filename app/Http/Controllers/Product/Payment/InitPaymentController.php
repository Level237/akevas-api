<?php

namespace App\Http\Controllers\Product\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use NotchPay\Payment;
use NotchPay\NotchPay;
class InitPaymentController extends Controller
{
    public function initPayment(Request $request){
        return $request;
    }

    private function initPaymentProcess(Request $request,$reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>"100",
                "productId"=>$request->productId,
                "phone"=>$request->phone,
                "hasVariation"=>$request->hasVariation,
                "productVariationId"=>$request->productVariationId,
                "attributeVariationId"=>$request->attributeVariationId,
                "quantity"=>$request->quantity,
                "price"=>$request->price,
                "quarter_delivery"=>$request->quarter_delivery,
                "address"=>$request->address,
                "shippingData"=>$request->shipping,
                "currency"=>"XAF",
                "reference"=>$reference,
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>env("NOTCHPAY_API_KEY")
            ])->post($url);
            $responseData=json_decode($response);
            return $responseData->transaction->reference;
    
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
                    "channel" => $request->methodChanel,
                    "data" => [
                        "phone" => "+237".$request->phone,
                    ]
                    ],
                ),'application/json')->withHeaders([
                    "Authorization"=>env("NOTCHPAY_API_KEY")
                ])->post($url);
                $responseData=json_decode($response);
                return $responseData->status;
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
