<?php

namespace App\Http\Controllers\User;


use App\Models\Order;
use App\Models\ShopVisit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StatShopController extends Controller
{
    public function currentStats(){
        $user=Auth::guard('api')->user();
        
        $orders=Order::where('user_id',$user->id)->get();
        $total_orders=$orders->count();
        $total_amount=$orders->sum('total');
        $orders_in_progress=$orders->where('status','0')->count();
        return response()->json(['total_orders'=>$total_orders,'total_amount'=>$total_amount,'orders_in_progress'=>$orders_in_progress]);
    }
}
