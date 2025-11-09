<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CheckTokenValidityController extends Controller
{
    public function checkIsAuthenticated(Request $request)
{
            $origin = $request->headers->get('origin');

        // 2️⃣ Déterminer quel cookie utiliser selon le sous-domaine
        if (str_contains($origin, 'seller.akevas.com')) {
            $cookieNameAccess = 'accessTokenSeller';
        } elseif (str_contains($origin, 'delivery.akevas.com')) {
            $cookieNameAccess = 'accessTokenDelivery';
        } elseif (str_contains($origin, 'localhost')) {
            // En local, on suppose qu’on teste le vendeur
            $cookieNameAccess = 'accessTokenSeller';
        } else {
            $cookieNameAccess = 'accessToken';
        }

        // 3️⃣ Vérifier si le cookie du bon type est présent
        $tokenFound = $request->cookie($cookieNameAccess);

        if (!$tokenFound) {
            return response()->json([
                'isAuthenticated' => false,
                'reason' => "no_cookie_for_{$cookieNameAccess}",
                'host' => $origin
            ]);
        }

        // 4️⃣ Injecter le token dans le header Authorization
        $request->headers->set('Authorization', 'Bearer ' . $tokenFound);

        // 5️⃣ Authentifier via le guard 'api'
        $user = Auth::guard('api')->user();

        if ($user) {
            return response()->json([
                'isAuthenticated' => true,
                'role' => $user->role ?? 'unknown',
                'domain' => $origin,
            ]);
        }

        // 6️⃣ Token invalide ou expiré
        return response()->json([
            'isAuthenticated' => false,
            'reason' => 'invalid_or_expired_token',
            'host' => $origin
        ]);

}
}
