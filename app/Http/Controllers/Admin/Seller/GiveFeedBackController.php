<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GiveFeedBackController extends Controller
{
    public function giveFeedBack($user_id,Request $request){
        $feedBack=new FeedBack;
        $feedBack->user_id=$user_id;
        $feedBack->message=$request->message;
        $feedBack->save();

        return response()->json(['message',"Feedback envoyé"]);
    }
}
