<?php

namespace App\Repositories\Auth;

use App\Models\User;

class RegisterRepository{

    public function register($data){

        $user=new User;
        $user->phone_number=$data["phone_number"];
        $user->role_id=3;
        $user->password=$data["password"];
        $user->userName=$data["userName"];
        $user->email=$data["email"];
        $user->residence=$data['residence'];
        $user->save();

        return $user;


    }
}
