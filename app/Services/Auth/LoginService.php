<?php

namespace App\Services\Auth;

use App\Interfaces\Auth\LoginInterface;
use App\Repositories\Auth\LoginRepository;

class LoginService implements LoginInterface{

    public function login($data){

        $dataLogin=(new LoginRepository)->login($data);

        return $dataLogin;
    }
}
