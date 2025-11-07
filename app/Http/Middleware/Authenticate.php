<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // ⭐️ CORRECTION ⭐️
        
        // Si la requête NE s'attend PAS à une réponse JSON, 
        // nous retournons la route de connexion par défaut.
        if (! $request->expectsJson()) {
            return route('login'); // Assurez-vous d'avoir une route 'login' nommée
        }

        // Si la requête s'attend à du JSON (c'est-à-dire une requête API), 
        // nous retournons NULL.
        // C'est le comportement attendu pour une API. 
        // Laravel va alors lever l'exception d'authentification qui est interceptée 
        // par le gestionnaire d'exceptions et transformée en réponse JSON 401.
        return null; 
    }
}