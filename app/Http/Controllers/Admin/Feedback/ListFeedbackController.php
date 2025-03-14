<?php

namespace App\Http\Controllers\Admin\Feedback;


use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;

class ListFeedbackController extends Controller
{
    public function index(){
        $feedbacks=FeedBack::with('user')->get();
        return response()->json(FeedbackResource::collection($feedbacks));
    }
}
