<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListFeedbackController extends Controller
{
    public function index(){
        $feedbacks=Feedback::with('user')->get();

        return $feedbacks;
    }
}
