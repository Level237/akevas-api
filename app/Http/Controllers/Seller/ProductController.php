<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Product;
use App\Services\GenerateUrlResource;
use Illuminate\Http\Request;

class ProductController extends Controller
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
    public function store(ProductRequest $request)
    {
        $product=new Product;
        $product->product_name=$request->product_name;
        $product->product_url=(new GenerateUrlResource())->generateUrl($request->product_name);
        $product->product_description=$request->product_description;
        $product->shop_id=$request->shop_id;
        $product->product_price=$request->product_price;
        $product->product_quantity=$request->product_quantity;

        if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                $imageModel=Image::create(['image_path'=>$path]);
                $product->images()->attach($imageModel->id);
            }
            if ($request->filled('attributs')) {
                $product->attributes()->attach($imageModel->id);
            }
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
