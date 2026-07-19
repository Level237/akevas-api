<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ListShopReviewController extends Controller
{
    public function index()
    {
        $cacheKey = "reviews.shop.all";

        $reviews = Cache::remember($cacheKey, now()->addHours(1), function () {
            return ShopReview::with('user')->with('shop')->get();
        });
        return $reviews;
    }
}
