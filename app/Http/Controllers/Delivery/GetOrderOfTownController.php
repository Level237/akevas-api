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
        $ordArray=$this->getOrderOfQuarter($user->residence);
        return response()->json(OrderResource::collection($ordArray));
    }

    public function getOrderInQuarter($residence_id){

        
        $quarterInresidence=Quarter::where('id',intval($residence_id))->first();
       $quarterUser = Quarter::with('town')
            ->where('quarter_name', $quarterInresidence->quarter_name)
            ->first();
            
            $orders=Order::with('user')->where('fee_of_shipping',"!=",0)->where('status',"!=","2")->orderBy('created_at','desc')->where('quarter_delivery',$quarterUser->quarter_name)->get();
           
            
        return response()->json(OrderResource::collection($orders));
    }

    private function getOrderOfQuarter($residence){
        $quarterInresidence=Quarter::where('id',intval($residence))->first();
       $quarterUser = Quarter::with('town')
            ->where('quarter_name', $quarterInresidence->quarter_name)
            ->first();
            $ordArray=[];
            $orders=Order::with('user')->where('fee_of_shipping',"!=",0)->where('status',"!=","2")->orderBy('created_at','desc')->get();
            foreach($orders as $order){
                if($order->quarter_delivery){
                    $quarter=Quarter::where('quarter_name',$order->quarter_delivery)->first();
                    if($quarter->town_id==$quarterUser->town->id){
                     $ordArray[]=$order;
                    }
                }
            }
            return $ordArray;
    }
}
