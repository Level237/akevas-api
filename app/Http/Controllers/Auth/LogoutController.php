<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class LogoutController extends Controller
{
    public function logout(){
       $user = Auth::guard('api')->user();
        $user->token()->revoke();
        $domain = (config('app.env') === 'production') ? '.akevas.com' : null;
    $secure = config('app.env') === 'production';
    
    // 3. Définir la date d'expiration dans le passé (expire immédiatement)
    $pastExpiration = Carbon::now()->subMinutes(5)->timestamp;

    // 4. Construire la réponse (statut 204 No Content est courant pour le logout)
    return response()->noContent(204)
        // 5. Faire expirer l'accessToken
        ->cookie('accessToken', null, 
            $pastExpiration, 
            '/', $domain, $secure, true, false, 'none') // Utiliser les mêmes paramètres que la pose
        // 6. Faire expirer le refreshToken
        ->cookie('refreshToken', null, 
            $pastExpiration, 
            '/', $domain, $secure, true, false, 'none');
    }
}
