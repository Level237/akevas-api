<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRepository
{

    public function login($data)
    {

        $user = User::where("phone_number", $data["phone_number"])->first();
        if ($data['password']) {
            $hashedPassword = $user->password;
            if (!Hash::check($data['password'], $hashedPassword)) {
                return response()->json(['code' => '404']);
            }
        }
        return $user;


    }
}
