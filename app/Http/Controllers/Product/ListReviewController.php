<?php

namespace App\Http\Controllers\Product;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Cache;
class ListReviewController extends Controller
{
    public function index($product_id)
    {
        $cacheKey = "reviews.product.{$product_id}";

        $reviews = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($product_id) {
            return Review::where('product_id', $product_id)
                ->where('is_approved', 1)
                ->latest('created_at')
                ->get();
        });

        return response()->json(ReviewResource::collection($reviews));
    }
}
