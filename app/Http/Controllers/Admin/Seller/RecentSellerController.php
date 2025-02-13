<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;

class RecentSellerController extends Controller
{
    public function recentSeller(){

        return SellerResource::collection(User::where('role_id',2)->get());
    }
}
