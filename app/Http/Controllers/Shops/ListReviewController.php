<?php

namespace App\Http\Controllers\Shops;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopReviewResource;
use Illuminate\Support\Facades\Cache;
class ListReviewController extends Controller
{
    public function index($shop_id)
    {

        $cacheKey = "reviews.shop.{$shop_id}";

        $reviews = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($shop_id) {
            return ShopReview::where('shop_id', $shop_id)
                ->where('is_approved', 1)
                ->latest('created_at')
                ->get();
        });

        return response()->json(ShopReviewResource::collection($reviews));
    }
}
