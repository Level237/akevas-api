<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
     public function currentUser(){
        $User=Auth::guard('api')->user();
        return $User;
    }
}
