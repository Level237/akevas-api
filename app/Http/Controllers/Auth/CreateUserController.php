<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateUserController extends Controller
{
    public function registerSeller(Request $request){
        $valid = validator($request->only('phone_number','password','lastName'), [
                'phone_number' => 'required|string|exists:users',
                'password' => 'required|string',
                'lastName'=>'required'
            ]);

              if ($valid->fails()) {
                return response()->json(['error'=>$valid->errors()], 400);

            }
            $data = request()->only('lastName','password','phone_number');
            $client=(new GetClientRepository())->getClient();

            $request->request->add([
            "grant_type" => "password",
            "client_id"=>$client->id,
            'client_secret' => $client->secret,
            'username'      => $request->phone_number,
            'password'      => $request->password,
            "scope"         =>"seller"
        ]);

          $token = Request::create(
        'oauth/token',
        'POST'
    );
    return Route::dispatch($token);
    
    }
}
