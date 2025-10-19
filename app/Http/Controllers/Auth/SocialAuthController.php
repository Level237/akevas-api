<?php

namespace App\Http\Controllers\Auth;

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
        $token = $tokenResult->accessToken;

        return redirect("{$frontendUrl}/auth/callback?token={$token}&user_id={$user->id}&role_id={$user->role_id}");
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
