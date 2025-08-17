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

    public function getNotification($notificationId){
        $user=Auth::guard("api")->user();
        $notification = $user->notifications()->where('id', $notificationId)->first();

         if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            
            return $notification;
        }

        return $notification;
    }
}
