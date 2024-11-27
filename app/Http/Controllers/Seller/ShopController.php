<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Models\Shop;
use App\Service\Shop\generateShopNameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopRequest $request)
    {
        try{

            $shop=new Shop;
            $shop->shop_name=$request->shop_name;
            $shop->user_id=Auth::user()->id;
            $shop->shop_key=(new generateShopNameService())->generateShopName();
            $shop->shop_description=$request->shop_description;
            $shop->shop_type_id=$request->shop_type_id;
            $file = $request->file('shop_profile');
            $image_path = $file->store('shops', 'public');
            $shop->shop_profile=$image_path;
            $shop->save();

            return response()->json(['message'=>"shop created successfully"],200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
              ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
