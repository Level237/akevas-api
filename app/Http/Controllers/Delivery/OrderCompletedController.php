<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderCompletedController extends Controller
{
    public function orderCompleted($order_id){
        $order=Order::find($order_id);
        $order->status=2;
        $order->save();
        return response()->json(["message"=>"Order completed"]);
    }
}
