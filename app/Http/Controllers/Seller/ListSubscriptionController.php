<?php

namespace App\Http\Controllers\Seller;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;

class ListSubscriptionController extends Controller
{
    public function index(){

        $subscriptions=SubscriptionResource::collection(Subscription::all());
        return response()->json($subscriptions);
    }
}
