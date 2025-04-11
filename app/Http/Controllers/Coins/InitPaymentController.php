<?php

namespace App\Http\Controllers\Coins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitPaymentController extends Controller
{
    public function initPaymentCoin(){

        try{
            $url = "https://api.notchpay.co/payments/initialize";
            
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
