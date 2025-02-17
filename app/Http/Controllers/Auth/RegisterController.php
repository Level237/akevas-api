<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Auth\RegisterService;

class RegisterController extends Controller
{
        public function register(Request $request){

        try{
            $valid = validator($request->only('phone_number','password'), [
                'phone_number' => 'required',
                'password' => 'required|string',
            ]);

            if ($valid->fails()) {
                return response()->json(['error'=>$valid->errors()], 400);
            }
            $data = request()->only('phone_number','password','userName','email');
            $registerUser=(new RegisterService())->register($data);
            $client=(new GetClientRepository())->getClient();
            $tokenUser=(new GenerateTokenUserService())->generate($client,$registerUser,$data['password'],$request);
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
