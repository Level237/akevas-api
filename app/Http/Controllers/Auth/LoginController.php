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
             $valid = validator($request->only('phone_number','password','role_id'), [
                'phone_number' => 'required|string|exists:users',
                'password' => 'required|string',
                'role_id' => 'required|integer|exists:roles,id',
            ]);

            if ($valid->fails()) {
                return response()->json(['error'=>"le numero de telephone ou le mot de passe sont incorrect"], 400);
            }
            $data = request()->only('phone_number','password');
            $loginUser=(new LoginService())->login($data);
            $client=(new GetClientRepository())->getClient();

            if($request->role_id != $loginUser['role_id']){
                return response()->json(['error'=>"vous n'avez pas les droits d'acces à cette application"], 403);
            }
            $tokenUser=(new GenerateTokenUserService())->generate($client,$loginUser,$data['password'],$request);
            return $tokenUser;
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
              ], 500);
        }
    }
}