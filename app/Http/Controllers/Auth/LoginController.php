<?php

namespace App\Http\Controllers\Auth;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use App\Http\Controllers\Controller;

use App\Repositories\GetClientRepository;
use App\Services\Auth\GenerateTokenUserService;

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
                return response()->json(['message'=>"vous n'avez pas les droits d'acces à cette application"], 403);
            }
            $tokenUser=(new GenerateTokenUserService())->generate($client,$loginUser,$data['password'],$request);
            
            $tokenData = json_decode($tokenUser->getContent(), true);

            if ($tokenUser->getStatusCode() === 200) {
                $accessToken = $tokenData['access_token'];
                $refreshToken = $tokenData['refresh_token'];

                $domain = (config('app.env') === 'production') ? '.akevas.com' : null;
                $secure = config('app.env') === 'production';

                return response()->noContent(204)->cookie('accessToken', $accessToken, 
                Carbon::now()->addMinutes(config('passport.token_ttl'))->timestamp, 
                '/', $domain, $secure, true, false, 'none')
            ->cookie('refreshToken', $refreshToken, 
                Carbon::now()->addDays(30)->timestamp,
                '/', $domain, $secure, true, false, 'none');
            }
            
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
              ], 500);
        }
    }
}