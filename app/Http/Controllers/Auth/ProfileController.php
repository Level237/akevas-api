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
}
