<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CheckIfInputExistInDatabaseController extends Controller
{
    public function checkEmailAndPhoneNumber(Request $request)
    {
        $email = $request->email;
        $phoneNumber = $request->phone;

        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json(['message' => 'Cet email est déjà utilisé'], 400);
        }

        $user = User::where('phone_number', $phoneNumber)->first();
        if ($user) {
            return response()->json(['message' => 'Ce numéro de téléphone est déjà utilisé'], 400);
        }

        return response()->json(["status" => "success", "message" => "Email and phone number are not in the database"], 200);
    }
}
