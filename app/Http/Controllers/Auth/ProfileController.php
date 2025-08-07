<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
     public function currentUser(){
        $User=Auth::guard('api')->user();
        $user=UserResource::make($User);
        
         return response()->json($user);
    }

    public function updateUser(Request $request){
        $user=Auth::guard('api')->user();
        $user->userName=$request->userName;
        $user->phone_number=$request->phone_number;
        $user->email=$request->email;
        $user->save();
        return response()->json($user);
    }
}
