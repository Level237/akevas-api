<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\NewSellerRequest;
use App\Services\Shop\generateShopNameService;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class CreateSellerController extends Controller
{
    public function create(Request $request){

        
         try{
        $seller=new User;
        $seller->firstName=$request->firstName;
        $seller->lastName=$request->lastName;
        $seller->email=$request->email;
        $seller->phone_number=$request->phone_number;
        $seller->birthDate=$request->birthDate;
        $seller->isWholesaler=$request->isWholesaler;
        $seller->role_id=2;
        $seller->nationality=$request->nationality;

        $file_cni_front = $request->file('identity_card_in_front');
        $image_path_cni_front = $file_cni_front->store('cni/front', 'public');
        $seller->identity_card_in_front=$image_path_cni_front;

        $file_cni_back = $request->file('identity_card_in_back');
        $image_path_cni_back = $file_cni_back->store('cni/back', 'public');
        $seller->identity_card_in_back=$image_path_cni_back;

        $file_cni_with_the_person = $request->file('identity_card_with_the_person');
        $image_path_cni_with_the_person = $file_cni_with_the_person->store('cni/person', 'public');
        $seller->identity_card_with_the_person=$image_path_cni_with_the_person;

        $seller->password=Hash::make($request->password);
        $seller->isWholesaler=$request->isWholesaler;
        if( $seller->save()){
            $shop=new Shop;
            $shop->shop_name=$request->shop_name;
            $shop->shop_key=(new generateShopNameService)->generateUniqueShopName($request->shop_name);
            $shop->shop_description=$request->shop_description;
            $shop->user_id=$seller->id;
            $shop->town_id=intval($request->town_id);
            $shop->quarter_id=intval($request->quarter_id);
            $shop->product_type=$request->product_type;
            $shop->shop_gender=(string)$request->shop_gender;
            $shop_profile = $request->file('shop_profile');
            $shop->shop_profile=$shop_profile->store('shop/profile','public');
            
            }
            if($shop->save()){
                foreach($request->categories as $category){
                $shop->categories()->attach($category);
            }
            foreach($request->images as $image){
                $i=new Image;
                $i->image_path=$image->store('shop/images','public');
                if($i->save()){
                    $shop->images()->attach($i);
                }
                
            }
            return response()->json(['message'=>"seller created successfully",'success'=>true],201);
        }
        

        
       }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'errors' => $e
          ], 500);
    }
            
    }

}