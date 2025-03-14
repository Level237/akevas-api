<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListReviewController extends Controller
{
    public function index(){
        $reviews=Review::with('user')->get();

        return $reviews;
    }
}
