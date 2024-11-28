<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ValidateSellerController extends Controller
{
    public function validateSeller(Request $request,$id){
        try{
            $seller=User::find($id);
            $seller->isSeller=$request->isSeller;
            $seller->save();
            return response()->json(['message'=>"Seller validated successfully"],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
              ], 500);
        }
    }
}
