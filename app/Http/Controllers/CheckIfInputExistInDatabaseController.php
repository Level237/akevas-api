<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CheckIfInputExistInDatabaseController extends Controller
{
    public function checkEmailAndPhoneNumber(Request $request)
    {
        $email = $request->email;
        $phoneNumber = $request->phoneNumber;

        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json(['message' => 'Email already exists'], 400);
        }

        $user = User::where('phoneNumber', $phoneNumber)->first();
        if ($user) {
            return response()->json(['message' => 'Phone number already exists'], 400);
        }

        return response()->json(['message' => 'Email and phone number are not in the database'], 200);
    }
}
