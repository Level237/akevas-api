<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ListNotificationController extends Controller
{
    public function list(){
        $users=Auth::guard("api")->user();

        $allNotifications=$user->notifications;

        return $allNotifications;
    }
}
