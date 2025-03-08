<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActiveDeliveryStatController extends Controller
{
    public function activeDeliveryStat(){
       $activeDeliveries=User::where("created_at",">",now()->subDays(30))->where("role_id",4)->count();
        $totalDeliveries=User::where("role_id",4)->count();
        $activeOrders=Order::where("created_at",">",now()->subDays(30))->where("status",2)->count();
        return response()->json([
            "activeDeliveries"=>$activeDeliveries,
            "totalDeliveries"=>$totalDeliveries,
            "activeOrders"=>$activeOrders
        ]);
    }
}