<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Repositories\GetClientRepository;
use App\Services\Auth\GenerateTokenUserService;
use App\Services\Auth\LoginService;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request){

        try{
            $valid = validator($request->only('phone_number','password'), [
                'phone_number' => 'required|string|exists:users',
                'password' => 'required|string',
            ]);

            if ($valid->fails()) {
                return response()->json(['error'=>$valid->errors()], 400);
            }
            $data = request()->only('phone_number','password');
            $loginUser=(new LoginService())->login($data);
            $client=(new GetClientRepository())->getClient();
            $tokenUser=(new GenerateTokenUserService())->generate($client,$loginUser,$data['password'],$request);
            return $tokenUser;
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
              ], 500);
        }
    }
}