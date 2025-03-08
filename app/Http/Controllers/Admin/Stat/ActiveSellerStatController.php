<?php

namespace App\Http\Controllers\Admin\Stat;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActiveSellerStatController extends Controller
{
    public function activeSellerStat(){
        $activeSellers=User::where("created_at",">",now()->subDays(30))->where("role_id",2)->count();
        $totalSellers=User::where("role_id",2)->count();
        $activeProducts=Product::where("created_at",">",now()->subDays(30))->count();
        return response()->json([
            "activeSellers"=>$activeSellers,
            "totalSellers"=>$totalSellers,
            "activeProducts"=>$activeProducts
        ]);
    }
}
