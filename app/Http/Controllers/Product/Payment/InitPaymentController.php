<?php

namespace App\Http\Controllers\Product\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function initPayment(Request $request){
        $url = "https://api.notchpay.co/payments/initialize";
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>"brams@gmail.com",
                "amount"=>"100",
                "currency"=>"XAF",
                "reference"=>"REFID".rand(123456, 999999),
                "callback"=>"http://localhost:5173?method=".$request->paymentMethod."&total=".$request->total."&shipping=".$request->shipping."&productId=".$request->productId."&quantity=".$request->quantity."&name=".$request->name."&price=".$request->price."&quarter=".$request->quarter,
                    
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>"pk_test.aucN1k448sPoYBdotSJJ6U5IDXDFDFAhUbNSdVguuwHAuhBIGVC0NHTQgEl30m3Xtq83aqpSvq9rFA1VPi7cRUiaKr0fAh64xFLxIqiof4y7tZ6TuJ9FM4cUHs5Av"
            ])->post($url);

            return json_decode($response);
    }
}
