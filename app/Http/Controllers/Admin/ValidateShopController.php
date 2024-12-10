<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ValidateShopController extends Controller
{
    public function validateShop(Request $request,$id){
            try{
                $shop=Shop::find($id);
                $shop->status=$request->status;
                $shop->expire=Carbon::now()->addDay(7);
                $shop->save();
            return response()->json(['message'=>"Shop validated successfully"],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
              ], 500);
        }

    }
}
