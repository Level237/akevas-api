<?php

namespace App\Http\Controllers\Auth;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use Illuminate\Support\Facades\Log;

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
             $origin = $request->headers->get('origin');
            
            if ($tokenUser->getStatusCode() === 200) {
                $accessToken = $tokenData['access_token'];
                $refreshToken = $tokenData['refresh_token'];

                $domain = '.akevas.com';
                $secure = config('app.env') === 'production';

                if (config('app.env') === 'production') {
                   

                    if (str_contains($origin, 'seller.akevas.com')) {
                        
        $cookieNameAccess = 'accessTokenSeller';
         
        $cookieNameRefresh = 'refreshTokenSeller';
    } elseif (str_contains($origin, 'delivery.akevas.com')) {
        $cookieNameAccess = 'accessTokenDelivery';
        $cookieNameRefresh = 'refreshTokenDelivery';
    } else if (str_contains($origin, 'localhost')) {
        $cookieNameAccess = 'accessToken';
        $cookieNameRefresh = 'refreshToken';
    } else {
        $cookieNameAccess = 'accessToken';
        $cookieNameRefresh = 'refreshToken';
    }
                }
                Log::info('Seller origin: ' . $cookieNameAccess,[
                    'cookieNameAccess' => $cookieNameAccess,
                    'cookieNameRefresh' => $cookieNameRefresh,
                    'accessToken' => $accessToken,
                    'refreshToken' => $refreshToken,
                    'domain' => $domain,
                    'secure' => $secure,
                ]);
                return response()->json(['message' => 'Login success'], 200)->cookie($cookieNameAccess, $accessToken, 
                config('passport.token_ttl'),
                '/', $domain, $secure, true, false, 'none')
            ->cookie($cookieNameRefresh, $refreshToken, 
                60*24*30,
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