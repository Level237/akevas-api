<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{


    public function redirectToGoogle(Request $request)
{
    // 1. Validation (s'assurer que l'URL d'origine est valide)
    $request->validate(['origin_url' => 'required|url']);
    $originUrl = $request->input('origin_url');

    // 2. Stocker l'URL du frontend dans la session (ou passer un paramètre crypté dans 'state')
    // Utiliser la session est le plus simple pour cet exemple
    session()->put('socialite_origin_url', $originUrl);

    // 3. Rediriger vers Google
    return Socialite::driver('google')->stateless()->redirect();
}
    public function handleGoogleCallback(): RedirectResponse
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

        try{
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error("Erreur de callback Google: " . $e->getMessage());
            return redirect("{$frontendUrl}/login?error=google_auth_failed");
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if($user){
            if (is_null($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            // Mettre à jour d'autres informations si nécessaire
            $user->save();
        }

        $scope = $this->getUserScope($user->role_id);
        $tokenResult = $user->createToken('GoogleAuthToken', [$scope]);
        $accessToken = $tokenResult->accessToken;

        // Refresh Token (Créer un jeton de durée de vie plus longue pour le rafraîchissement)
        $refreshToken = $user->createToken('GoogleRefreshToken', [], Carbon::now()->addDays(30))->accessToken;

        $domain = config('app.env') === 'production' ? parse_url(config('app.url'), PHP_URL_HOST) : null;
        $secure = config('app.env') === 'production';

        return redirect("{$frontendUrl}/auth/callback")->cookie('accessToken', $accessToken, 
        Carbon::now()->addMinutes(config('passport.token_ttl'))->timestamp, 
        '/', $domain, $secure, true, false, 'none') // ttl, path, domain, secure, httpOnly, raw, sameSite
    ->cookie('refreshToken', $refreshToken, 
        Carbon::now()->addDays(30)->timestamp, // Longue durée de vie
        '/', $domain, $secure, true, false, 'none');
    }

    protected function getUserScope(int $roleId): string
{
    switch ($roleId) {
        case 1:
            return "admin";
        case 2:
            return "seller";
        case 3:
            return "customer";
        case 4:
            return "delivery";
        default:
            return "customer"; // Scope par défaut
    }
}
}
