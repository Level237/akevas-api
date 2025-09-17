<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class GetPreferenceOrderController extends Controller
{
    public function getPreferenceOrders()
    {
       $user=Auth::guard('api')->user();
       $QuarterNameArray=[];
       $vehiclesQuarter=$user->vehicles[0]->quarters;
       
        foreach($vehiclesQuarter as $vehicle){
     
            $QuarterNameArray[]=$vehicle->quarter_name;
       }
       $orders=Order::whereIn('quarter_delivery',$QuarterNameArray)->where('fee_of_shipping',"!=",0)->where('isTake',"!=",1)->orderBy('created_at','desc')->get();
       return response()->json(OrderResource::collection($orders));


    }
}
