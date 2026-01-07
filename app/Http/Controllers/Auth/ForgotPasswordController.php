<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendForgotPasswordOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email non trouvé'], 422);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => now()
        ]);

        Mail::to($request->email)->send(new ForgotPasswordMail($otp));

        return response()->json(['message' => 'Code envoyé avec succès']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|numeric'
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        // Vérification si le code existe et n'a pas expiré (ex: 15 minutes)
        if (!$resetData || now()->subMinutes(15)->gt($resetData->created_at)) {
            return response()->json(['message' => 'Code invalide ou expiré'], 422);
        }

        // Ici, le code est bon. Tu peux :
        // - Soit réinitialiser le mot de passe ici si tu envoies le new_password dans la requête
        // - Soit renvoyer un token temporaire pour la vue suivante
        return response()->json(['message' => 'Code valide', 'status' => true]);
    }
}
