<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Interfaces\GenerateTokenInterface;

class GenerateTokenUserService implements GenerateTokenInterface{

    public function generate($clientData, $userData,$password,$request)
    {
        $scope="";

        if($userData['role_id']===1){
            $scope="admin";
        }else if($userData['role_id']===2){
            $scope="seller";
        }
        else if($userData['role_id']===3){
            $scope="customer";
        }else if($userData['role_id']===4){
            $scope="delivery";
        }
        $request->request->add([
            "grant_type" => "password",
            "client_id"=>$clientData->id,
            'client_secret' => $clientData->secret,
            'username'      => $userData['phone_number'],
            'password'      => $password,
            "scope"         =>$scope
        ]);

        // Fire off the internal request.
    $token = Request::create(
        'oauth/token',
        'POST'
    );
    return Route::dispatch($token);
        }
    }