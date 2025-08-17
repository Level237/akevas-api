<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ListNotificationController extends Controller
{
    public function list(){
        $user=Auth::guard("api")->user();

        $allNotifications=$user->notifications;

        return $allNotifications;
    }

    public function recentNotification(){
        $user=Auth::guard("api")->user();

        $allNotifications=$user->notifications()
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();

        return $allNotifications;
    }
}
