<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendForgotPasswordOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email non trouvé', "status" => 404], 422);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => now()
        ]);

        Mail::to($request->email)->send(new ForgotPasswordMail($otp));

        return response()->json(['message' => 'Code envoyé avec succès', "status" => 200]);
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

        $tempToken = Str::random(60);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => Hash::make($tempToken), // On le hash pour la sécurité
                'created_at' => now() // On reset le timer pour donner 15 min de plus
            ]);
        return response()->json([
            'success' => true,
            'temp_token' => $tempToken,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'temp_token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetData = DB::table('password_reset_tokens')->where('email', $request->email)->first();


        if (!$resetData || !Hash::check($request->temp_token, $resetData->token)) {
            return response()->json(['message' => 'Lien de réinitialisation invalide'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Votre mot de passe a été modifié avec succès !', "status" => 200]);
    }
}
