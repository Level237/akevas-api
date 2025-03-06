<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use App\Models\Quarter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class GetOrderOfTownController extends Controller
{
    public function getOrdersByTown()
    {
        $user=Auth::guard('api')->user();
        $quarterInresidence=Quarter::where('id',intval($user->residence))->first();
       $quarterUser = Quarter::with('town')
            ->where('quarter_name', $quarterInresidence->quarter_name)
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
