<?php

namespace App\Services\Auth;


use App\Interfaces\Auth\RegisterInterface;
use App\Repositories\Auth\RegisterRepository;



class RegisterService implements RegisterInterface{

    public function register($data){

        $dataRegister=(new RegisterRepository)->register($data);

        return $dataRegister;
    }
}
