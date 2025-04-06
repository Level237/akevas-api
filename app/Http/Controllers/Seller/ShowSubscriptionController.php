<?php

namespace App\Http\Controllers\Seller;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;

class ShowSubscriptionController extends Controller
{
    public function show($id){
        $subscription=Subscription::find($id);
        return response()->json(SubscriptionResource::make($subscription));
    }
}
