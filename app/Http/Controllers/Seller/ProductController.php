<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GenerateUrlResource;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
        $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $product = new Product;
            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();
            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }

            $product->product_name = $request->product_name;
            $product->product_url = (new GenerateUrlResource())->generateUrl($request->product_name);
            $product->product_description = $request->product_description;
            $product->shop_id = $shop->id;
            $product->product_price = $request->product_price;
            $product->product_quantity = $request->product_quantity;
            $product->product_gender = (string)$request->product_gender;
            $product_profile = $request->file('product_profile');
            $product->status = 1;
            $product->product_profile = $product_profile->store('product/profile', 'public');
            $product->whatsapp_number = $request->whatsapp_number;
            $product->product_residence = $request->product_residence;
            if ($product->save()) {
                if ($request->hasFile('images')) {
                    $images = $request->file('images');

                    foreach ($images as $image) {
                        $i = new Image;
                        $i->image_path = $image->store('product/images', 'public');
                        if ($i->save()) {
                            $product->images()->attach($i);
                        }
                    }
                    if ($request->filled('variants')) {
                        $variantNameMap = [];
                        $allAttributesData = [];

                        foreach (json_decode($request->variants) as $index => $variant) {
                            // Vérifier si le variant_name existe déjà
                            if (isset($variantNameMap[$variant->variant_name])) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Duplicate variant name found: ' . $variant->variant_name
                                ], 422);
                            }

                            $image_path = $request->variant_images[$index]->store("product/variants", "public");

                            // Stocker la première occurrence de chaque variant_name
                            $variantNameMap[$variant->variant_name] = true;

                            // Pour chaque variant, on ne prend que le premier attribute_value_id
                            if (!empty($variant->attribute_value_id)) {
                                $attribute = $variant->attribute_value_id[0]; // Prendre seulement le premier attribut
                                $allAttributesData[] = [
                                    'attribute_id' => $attribute,
                                    'quantity' => (string)$variant->stock,
                                    'price' => (string)$variant->price,
                                    'image_path' => $image_path,
                                    'variant_name' => $variant->variant_name
                                ];
                            }
                        }

                        // Supprimer d'abord toutes les anciennes relations
                        $product->attributes()->detach();

                        // Attacher les nouvelles relations
                        foreach ($allAttributesData as $data) {
                            $product->attributes()->attach($data['attribute_id'], [
                                'quantity' => $data['quantity'],
                                'price' => $data['price'],
                                'image_path' => $data['image_path'],
                                'variant_name' => $data['variant_name']
                            ]);
                        }
                    }

                    if ($request->has('categories') && is_array($request->categories)) {
                        $product->categories()->attach(array_map('intval', $request->categories));
                    }
                    if ($request->has('sub_categories') && is_array($request->sub_categories)) {
                        $product->categories()->attach(array_map('intval', $request->sub_categories));
                    }
                }
            }


            return response()->json(['message' => "Product created successfully"], 201);
        } catch (\Exception $e) {
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
