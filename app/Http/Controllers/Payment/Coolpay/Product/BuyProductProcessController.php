<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Payment\Coolpay\InitPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyProductProcessController extends Controller
{
    public function initPayment(Request $request){
        $initResponse=(new InitPaymentService())->initPay($request->price,"Achat Produit",$request->transaction_id,"89dd5e28-a639-4c66-92af-e8ee7ba95da7");

        return $initResponse;
    }

    public function paymentPending($product_id,$transaction_ref){
        $product=Product::find($product_id);
        $paymentExist=Payment::where('transaction_ref',$transaction_ref)->first();

        if(!isset($paymentExist)){
            $data=[
                'payment_type'=>"Momo",
                'price'=>$product->product_price,
                'payment_of'=>"product",
                'transaction_ref'=>$transaction_ref,
                'transaction_id'=>null,
                'status'=>"1",
                'user_id'=>Auth::user()->id
            ];
            $payment=event(new MakePaymentEvent($data));
            return response()->json(['message'=>"payment Pending"]);
    }
    }
}
