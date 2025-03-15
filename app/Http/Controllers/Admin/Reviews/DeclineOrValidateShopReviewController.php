<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeclineOrValidateShopReviewController extends Controller
{
    public function declineOrValidate($review_id,$status){
        $review=ShopReview::find($review_id);
        $review->is_approved=$status;
        $review->save();
        return response()->json(['message'=>true]);
    }
}
