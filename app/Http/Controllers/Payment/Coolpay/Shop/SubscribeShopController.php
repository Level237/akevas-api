<?php

namespace App\Http\Controllers\Payment\Coolpay\Shop;

use App\Http\Controllers\Controller;
use App\Services\Payment\Coolpay\InitPaymentService;
use Illuminate\Http\Request;

class SubscribeShopController extends Controller
{
    public function initPayment(Request $request){

        $initResponse=(new InitPaymentService())->initPay($request->price,"Abonnement boutique",$request->transaction_id,"424ed393-92b5-4ccb-b3d4-fe0465d33025");

        return $initResponse;
    }
}
