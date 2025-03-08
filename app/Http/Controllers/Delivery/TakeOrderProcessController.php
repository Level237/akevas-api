<?php

namespace App\Http\Controllers\Delivery;

use App\Models\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TakeOrderProcessController extends Controller
{
    public function takeOrder($order_id){

        $delivery=User::find(Auth::guard('api')->user()->id);
        $order=Order::find($order_id);
        $order->status="1";
        $order->isTake=1;
        $order->save();
        $delivery->processOrders()->attach($order_id,['isAccepted'=>true]);
        return response()->json(["message"=>"Order accepted"]);
    }
}
