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
    $state = base64_encode(json_encode([
        'origin_url' => $originUrl
    ]));
    // 3. Rediriger vers Google
    return Socialite::driver('google')->stateless()->with(['state' => $state]) ->redirect();
}
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
         $stateData = json_decode(base64_decode($request->input('state')), true);
        $originUrl = $stateData['origin_url'] ?? null;
        try{
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error("Erreur de callback Google: " . $e->getMessage());
            return redirect("{$originUrl}/login?error=google_auth_failed");
        }
        $email = $googleUser->getEmail();
        $user = User::where('email', $email)->first();
        $role_id=0;
        if($user){
             if (str_contains($originUrl, 'seller.akevas.com')) {
        $frontendUrl = "https://seller.akevas.com";
        $role_id = 2;
    } elseif (str_contains($originUrl, 'delivery.akevas.com')) {
        $frontendUrl = "https://delivery.akevas.com";
        $role_id = 4;
    } else {
        $frontendUrl = "https://akevas.com";
        $role_id = 3;
    }
           
            if (is_null($user->google_id)) {
                $user->google_id = $googleUser->getId();
            }
            // Mettre à jour d'autres informations si nécessaire
            $user->save();
        }else{
            $errorMsg = urlencode("Cette adresse email ({$email}) n'existe pas dans nos bases de données. Veuillez vous enregistrer d'abord.");
            return redirect("{$frontendUrl}/login?code=401");
        }

         if($user->role_id != $role_id){
           
                return redirect("{$frontendUrl}/login?code=500");
            }else{
                $scope = $this->getUserScope($user->role_id);
        $tokenResult = $user->createToken('GoogleAuthToken', [$scope]);
        $accessToken = $tokenResult->accessToken;

        // Refresh Token (Créer un jeton de durée de vie plus longue pour le rafraîchissement)
        $refreshToken = $user->createToken('GoogleRefreshToken', [], Carbon::now()->addDays(30))->accessToken;

        $domain = (config('app.env') === 'production') ? '.akevas.com' : null;
        $secure = config('app.env') === 'production';

        
        
        if(str_contains($originUrl, 'seller.akevas.com')){
            $cookieNameAccess = 'accessTokenSeller';
            $cookieNameRefresh = 'refreshTokenSeller';
        }elseif(str_contains($originUrl, 'delivery.akevas.com')){
            $cookieNameAccess = 'accessTokenDelivery';
            $cookieNameRefresh = 'refreshTokenDelivery';
        }else if (str_contains($originUrl, 'localhost')) {
        $cookieNameAccess = 'accessTokenSeller';
        $cookieNameRefresh = 'refreshTokenSeller';
    }else{
            $cookieNameAccess = 'accessToken';
            $cookieNameRefresh = 'refreshToken';
        }
        return redirect("{$frontendUrl}/authenticate")->cookie($cookieNameAccess, $accessToken, 
        config('passport.token_ttl'), 
        '/', $domain, $secure, true, false, 'none') // ttl, path, domain, secure, httpOnly, raw, sameSite
    ->cookie($cookieNameRefresh, $refreshToken, 
        60*24*30, // Longue durée de vie
        '/', $domain, $secure, true, false, 'none');
            }
        
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
