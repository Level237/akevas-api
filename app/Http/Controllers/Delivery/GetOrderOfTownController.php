<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use App\Models\Quarter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class GetOrderOfTownController extends Controller
{
    public function getOrders($quarter_residence)
    {
       $quarterUser = Quarter::with('town')
            ->where('quarter_name', $quarter_residence)
            ->first();
            $ordArray=[];
            $orders=Order::with('user')->get();
            foreach($orders as $order){
               $quarter=Quarter::where('quarter_name',$order->quarter_delivery)->first();
               if($quarter->town_id==$quarterUser->town->id){
                $ordArray[]=$order;
               }
            }
        return response()->json(OrderResource::collection($ordArray));
    }
}
