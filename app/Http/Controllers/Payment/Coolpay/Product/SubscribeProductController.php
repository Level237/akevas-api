<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\Payment\Coolpay\InitPaymentService;
use Illuminate\Http\Request;

class SubscribeProductController extends Controller
{
    public function initPayment(Request $request){

        $initResponse=(new InitPaymentService())->initPay($request->price,"Abonnement produit",$request->transaction_id,"56d271f5-8875-4911-b92d-0d12eb665ddf");

        return $initResponse;
    }

    public function paymentPending($membership_id,$product_id){
        $subscription_type=Subscription::find($membership_id);
        $product=Product::find($product_id);
    }
}
