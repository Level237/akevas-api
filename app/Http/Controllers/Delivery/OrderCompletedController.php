<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderCompletedController extends Controller
{
    public function orderCompleted($order_id,$duration){
        $order=Order::find($order_id);
        $order->status=2;
        $order->duration_of_delivery=$duration;
        $order->save();
        return response()->json(["message"=>"Order completed"]);
    }

    public function cancelOrder($order_id){
        $order=Order::find($order_id);
        $delivery=User::find(Auth::guard('api')->user()->id);
        $order->status="0";
        $order->isTake=0;
        $order->save();
        $delivery->processOrders()->detach($order_id);
        return response()->json(["message"=>"Order cancelled"]);
    }
}
