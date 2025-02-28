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
      $response = Route::dispatch($token);
    if ($response->getStatusCode() == 200) {
        $data = json_decode($response->getContent(), true);
        $token = $data['access_token'];

         $cookie = cookie('level_token', $token, 60 * 24 * 15); // 15 jours
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful'
        ])->withCookie($cookie);
    }

    return response()->json(['message' => 'Failed to generate token'], 401);
        }
    }
