<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;
use App\Services\GenerateUrlResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop=Shop::where('user_id',Auth::guard('api')->user()->id)->first();
        $products=Product::where('shop_id',$shop->id)->get();

        return $products;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            
            $product=new Product;
            $user=Auth::guard('api')->user();
            $shop=Shop::where('user_id',$user->id)->first();
            if(!$shop->products()->count()===0){
                $shop->shop_level="3";
                $shop->save();
            }
            
                $product->product_name=$request->product_name;
        $product->product_url=(new GenerateUrlResource())->generateUrl($request->product_name);
        $product->product_description=$request->product_description;
        $product->shop_id=$shop->id;
        $product->product_price=$request->product_price;
        $product->product_quantity=$request->product_quantity;
        $product_profile = $request->file('product_profile');
        $product->product_profile=$product_profile->store('product/profile','public');
        
        if($product->save()){
             if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                 $i=new Image;
                $i->image_path=$image->store('product/images','public');
                if($i->save()){
                    $product->images()->attach($i);
                }
            }
            //if ($request->filled('attributs')) {
                //$product->attributes()->attach($imageModel->id);
            //}

            if($request->has('categories') && is_array($request->categories)){
                $product->categories()->attach(array_map('intval', $request->categories));
            }
        }
        }
       
            
                return response()->json(['message'=>"Product created successfully"],201);
            
        
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
