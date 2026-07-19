<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class ListReviewController extends Controller
{
    public function index()
    {
        $cacheKey = "reviews.all";

        $reviews = Cache::remember($cacheKey, now()->addHours(1), function () {
            return Review::with('user')->with('product')->get();
        });

        return $reviews;
    }
}
