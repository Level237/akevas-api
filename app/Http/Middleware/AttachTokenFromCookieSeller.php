<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachAccessTokenFromCookieSeller
{
    
    public function handle(Request $request, Closure $next)
    {
        // 1. Vérifier si l'en-tête Authorization est déjà présent (pour ne pas écraser)
        if (!$request->headers->has('Authorization') && $request->cookie('accessTokenSeller')) {
            
            // 2. Lire le token depuis le cookie 'accessToken'
            $accessToken = $request->cookie('accessTokenSeller');
            
            // 3. Injecter le token dans l'en-tête Authorization au format Bearer
            $request->headers->set('Authorization', 'Bearer ' . $accessToken);
        }
        
        return $next($request);
    }
}
