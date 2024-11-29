<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewSellerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateSellerController extends Controller
{
    public function create(NewSellerRequest $request){

       try{
        $seller=new User;
        $seller->userName=$request->userName;
        $seller->role_id=2;
        $seller->town_id=$request->town_id;
        $seller->phone_number=$request->phone_number;
        $seller->email=$request->email;
        $seller->password=Hash::make($request->password);
        $seller->isWholesaler=$request->isWholesaler;

        $file = $request->file('profile');
        $image_path = $file->store('sellers', 'public');
        $seller->profile=$image_path;

        $cni_in_front = $request->file('cni_in_front');
        $image_path_cni_in_front = $cni_in_front->store('cni/front', 'public');
        $seller->cni_in_front=$image_path_cni_in_front;

        $cni_in_back = $request->file('cni_in_back');
        $image_path_cni_in_back = $cni_in_back->store('cni/back', 'public');
        $seller->cni_in_back=$image_path_cni_in_back;

        $seller->save();

        return response()->json(['message'=>"seller created successfully"],201);
       }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'errors' => $e
          ], 500);
    }
    }
}
