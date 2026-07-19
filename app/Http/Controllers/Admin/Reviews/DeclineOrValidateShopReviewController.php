<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class DeclineOrValidateShopReviewController extends Controller
{
    public function declineOrValidate($review_id, $status)
    {
        $review = ShopReview::find($review_id);
        $review->is_approved = $status;
        $review->save();
        Cache::forget("reviews.shop.{$review->shop_id}");
        return response()->json(['message' => true]);
    }
}
