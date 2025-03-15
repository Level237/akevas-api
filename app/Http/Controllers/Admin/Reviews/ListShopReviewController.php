<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListShopReviewController extends Controller
{
    public function index(){
        $reviews=ShopReview::with('user')->with('shop')->get();

        return $reviews;
    }
}
