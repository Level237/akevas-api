<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
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

    public function paymentPending($transaction_ref,$total,$order_array){

        $paymentExist=Payment::where('transaction_ref',$transaction_ref)->first();

        if(!isset($paymentExist)){
            $order=new Order;
            $order->user_id=Auth::guard('api')->user()->id;;
            $order->total=$total;
            if($order->save()){
                foreach($order_array as $order_detail){
                    $newOrderDetail=new OrderDetail;
                    $newOrderDetail->order_id=$order->id;
                    $newOrderDetail->product_id=$order_detail->product_id;
                    $newOrderDetail->order_product_quantity=$order_detail->order_product_quantity;
                    $newOrderDetail->unit_price=$order_detail->unit_price;
                    $newOrderDetail->save();

                    $data=[
                        'payment_type'=>"Momo",
                        'price'=>$total,
                        'payment_of'=>"product",
                        'transaction_ref'=>$transaction_ref,
                        'transaction_id'=>null,
                        'order_id'=>$order->id,
                        'status'=>"1",
                        'user_id'=>Auth::guard('api')->user()->id
                    ];
                    $payment=event(new MakePaymentEvent($data));
                }
                return response()->json(['message'=>"payment Pending"],200);
            }else{
                return response()->json(['message'=>"payment failed"],500);
            }
    }
    }

    public function buyProductCallBack(Request $request){
        $payment=Payment::where('transaction_ref',$request->transaction_ref)
        ->where('payment_of','=',"product")->first();

        if($request->transaction_status==="SUCCESS"){
            $order=Order::find($payment->order_id);
            $payment->status="2";
            $payment->save();
            $order->isPay=1;
            $order->save();
        }else if($request->transaction_status==="FAILED"){
            $payment->status="1";
            $payment->save();
        }
    }
}
