<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachAccessTokenFromCookieUser
{
    
    public function handle(Request $request, Closure $next)
    {
        $host = $request->headers->get('origin');

         if (str_contains($host, 'seller.akevas.com')) {
            $cookieName = 'accessTokenSeller';
        } elseif (str_contains($host, 'delivery.akevas.com')) {
            $cookieName = 'accessTokenDelivery';
        } elseif (str_contains($host, 'localhost:5173')) {
            $cookieName = 'accessToken';
        } 
        else {
            $cookieName = 'accessToken'; // client / admin
        }
        // 1. Vérifier si l'en-tête Authorization est déjà présent (pour ne pas écraser)
        if (!$request->headers->has('Authorization') && $request->cookie($cookieName)) {
            // 2. Lire le token depuis le cookie 'accessToken'
            $accessToken = $request->cookie($cookieName);
            // 3. Injecter le token dans l'en-tête Authorization au format Bearer
            $request->headers->set('Authorization', 'Bearer ' . $accessToken);
        }
        
        return $next($request);
    }
}
