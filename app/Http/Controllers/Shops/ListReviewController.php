<?php

namespace App\Http\Controllers\Shops;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopReviewResource;

class ListReviewController extends Controller
{
    public function index($shop_id){
        $reviews=ShopReview::where('shop_id',$shop_id)->get();

        return response()->json(ShopReviewResource::collection($reviews));
    }
}
