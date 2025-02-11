<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CheckTokenValidityController extends Controller
{
    public function checkToken(Request $request)
{
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json(['valid' => 0], 200);
    }

    // Tenter de récupérer l'utilisateur associé au token
    $user = Auth::guard('api')->user();

    if ($user) {
        return response()->json(['valid' => 1],200);
    }

    return response()->json(['valid' => 0], 200);
}
}
