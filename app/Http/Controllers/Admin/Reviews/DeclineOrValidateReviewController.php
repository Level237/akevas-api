<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class DeclineOrValidateReviewController extends Controller
{
    public function declineOrValidate($review_id, $status)
    {
        $review = Review::find($review_id);
        $review->is_approved = $status;
        $review->save();
        Cache::forget("reviews.product.{$review->product_id}");
        return response()->json(['message' => true]);
    }
}
