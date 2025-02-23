<?php

namespace App\Http\Controllers\Admin\Delivery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Models\User;

class RecentDeliveryController extends Controller
{
     public function recentDelivery(){

        return DeliveryResource::collection(User::where('role_id',4)->orderBy('created_at', 'desc')->take(2)->get());
    }
}
