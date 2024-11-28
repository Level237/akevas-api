<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ValidateProductController extends Controller
{
    public function validateProduct(Request $request,$id){
        try{
            $product=Product::find($id);
            $product->status=$request->status;
            $product->save();
            return response()->json(['message'=>"Product validated successfully"],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
              ], 500);
        }
    }
}
