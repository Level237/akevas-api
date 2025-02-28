<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
   protected $tokenRepository;
    protected $refreshTokenRepository;

    public function __construct(
        TokenRepository $tokenRepository,
        RefreshTokenRepository $refreshTokenRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function refresh(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = auth()->user();
            
            // Révoquer l'ancien token
            if ($request->cookie('level_token')) {
                $this->tokenRepository->revokeAccessToken($request->cookie('level_token'));
            }
             $scope = $this->determineUserScope($user);
            // Créer un nouveau token
            $token = $user->createToken('authToken',[$scope])->accessToken;
            
            // Créer un nouveau cookie
            $cookie = cookie('level_token', $token, 60 * 24 * 15); // 15 jours
            
            return response()
                ->json(['message' => 'Token refreshed successfully'])
                ->withCookie($cookie);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Refresh token failed'], 401);
        }
    }

    private function determineUserScope($user)
    {
        // Ajustez cette logique selon votre modèle utilisateur
        // Par exemple, si vous avez une colonne 'role' dans votre table users
        switch ($user->role) {
            case 'admin':
                return 'admin';
            case 'seller':
                return 'seller';
            case 'delivery':
                return 'delivery';
            default:
                return 'customer';
        }
    }
}
