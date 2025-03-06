<?php

namespace App\Http\Controllers\Delivery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DeliveryResource;

class ProfileController extends Controller
{
    public function currentDelivery(){
        $User=Auth::guard('api')->user();
        $user=DeliveryResource::make($User);
        
         return response()->json($user);
    }
}
