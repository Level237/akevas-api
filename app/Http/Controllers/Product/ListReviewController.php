<?php

namespace App\Http\Controllers\Product;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;

class ListReviewController extends Controller
{
    public function index($product_id){

        $reviews=Review::where('product_id',$product_id)->get();

        return response()->json(ReviewResource::collection($reviews));
    }
}
