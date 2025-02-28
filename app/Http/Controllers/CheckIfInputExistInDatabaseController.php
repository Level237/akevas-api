<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class CheckIfInputExistInDatabaseController extends Controller
{
    public function checkEmailAndPhoneNumber(Request $request)
    {
        $email = $request->email;
        $phoneNumber = $request->phone;

        if(isset($request->userName)){
             $userName=$request->userName;
             $userInName = User::where('userName', $userName)->first();
        if ($userInName) {
            return response()->json(["message' => 'Ce nom d'utilisateur est déjà utilisé"], 400);
        }
        }
       

        $userMail = User::where('email', $email)->first();
        if ($userMail) {
            return response()->json(['message' => 'Cet email est déjà utilisé'], 400);
        }

        $userPhone = User::where('phone_number', $phoneNumber)->first();
        if ($userPhone) {
            return response()->json(['message' => 'Ce numéro de téléphone est déjà utilisé'], 400);
        }
        
        return response()->json(["status" => "success", "message" => "Email and phone number are not in the database"], 200);
    }

}
