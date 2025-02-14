<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SellerResource;

class CurrentSellerController extends Controller
{
    public function currentSeller(){

        $user=Auth::guard('api')->user();
        return SellerResource::make(User::find($user->id));
    }
}
