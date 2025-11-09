<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // 🔹 Récupérer le user connecté
        $user = Auth::guard('api')->user();

        if ($user && $user->token()) {
            $user->token()->revoke();
        }

        // 🔹 Déterminer l'origine / host
        $origin = $request->headers->get('origin') ?? $request->getHost();

        // 🔹 Définir les noms de cookies selon le sous-domaine
        if (str_contains($origin, 'seller.akevas.com')) {
            $cookieNameAccess = 'accessTokenSeller';
            $cookieNameRefresh = 'refreshTokenSeller';
        } elseif (str_contains($origin, 'delivery.akevas.com')) {
            $cookieNameAccess = 'accessTokenDelivery';
            $cookieNameRefresh = 'refreshTokenDelivery';
        } elseif (str_contains($origin, 'localhost')) {
            // cas local
            $cookieNameAccess = 'accessTokenSeller';
            $cookieNameRefresh = 'refreshTokenSeller';
        } else {
            // domaine par défaut (client ou autre)
            $cookieNameAccess = 'accessToken';
            $cookieNameRefresh = 'refreshToken';
        }

        // 🔹 Déterminer le domaine du cookie (production ou local)
        $domain = (config('app.env') === 'production') ? '.akevas.com' : null;
        $secure = config('app.env') === 'production';

        // 🔹 Expiration passée pour supprimer le cookie
        $expiredAt = Carbon::now()->subMinutes(5)->timestamp;

        // 🔹 Retourner la réponse avec suppression des deux cookies
        return response()->json(['message' => 'Déconnexion réussie.'], 200)
            ->cookie(
                $cookieNameAccess,
                null,
                $expiredAt,
                '/',
                $domain,
                $secure,        // secure: true en production
                true,           // httpOnly
                false,          // raw
                'none'          // sameSite
            )
            ->cookie(
                $cookieNameRefresh,
                null,
                $expiredAt,
                '/',
                $domain,
                $secure,
                true,
                false,
                'none'
            );
    }
}
