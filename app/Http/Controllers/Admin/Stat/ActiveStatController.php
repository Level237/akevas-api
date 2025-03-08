<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActiveStatController extends Controller
{
    public function activeStat(){
        $active=Order::where("created_at",">",now()->subDays(30))->count();
        $totalPrice=Order::where("created_at",">",now()->subDays(30))->sum("total");
        $totalProducts=Product::count();
        return response()->json([
            "activeOrders"=>$active,
            "revenues"=>$totalPrice,
            "totalProducts"=>$totalProducts
        ]);
        
    }
}
