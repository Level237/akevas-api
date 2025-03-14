<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeclineOrValidateReviewController extends Controller
{
    public function declineOrValidate($review_id,$status){
        $review=Review::find($review_id);
        $review->is_approved=$status;
        $review->save();
        return response()->json(['message'=>true]);
    }
}
