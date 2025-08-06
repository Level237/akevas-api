<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;

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
        $order->status="0";
        $order->isTake=0;
        $order->save();
        return response()->json(["message"=>"Order cancelled"]);
    }
}
