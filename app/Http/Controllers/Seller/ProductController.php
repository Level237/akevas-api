<?php

namespace App\Http\Controllers\Seller;

use Exception;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GenerateUrlResource;
use App\Models\ProductAttributesValue;
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
            DB::beginTransaction();
    
            // Récupération du shop
            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();
    
            // Mise à jour du niveau du shop si c'est le premier produit
            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }
    
            // Création du produit
            $product = new Product;
            $product->product_name = $request->product_name;
            $product->product_url = (new GenerateUrlResource())->generateUrl($request->product_name);
            $product->product_description = $request->product_description;
            $product->shop_id = $shop->id;
            $product->type = $request->type == 'simple' ? 0 : 1; // 'simple' ou 'variable'
            $product->product_gender = $request->product_gender;
            $product->whatsapp_number = $request->whatsapp_number;
            $product->product_residence = $request->product_residence;
            $product->status = 1;
    
            // Gestion du produit simple
            if ($product->type ==0) {
                $product->product_price = $request->product_price;
                $product->product_quantity = $request->product_quantity;
            }
    
            // Gestion de l'image principale
            if ($request->hasFile('product_profile')) {
                $product->product_profile = $request->file('product_profile')->store('product/profile', 'public');
            }
    
            $product->save();
    
            // Gestion des images du produit simple
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product/images', 'public');
                    $product->images()->create(['image_path' => $imagePath]);
                }
            }
    
            // Gestion des variations pour produit variable
            if ($product->type == 1 && $request->filled('variations')) {
                $variations = json_decode($request->variations, true);
                
                foreach ($variations as $colorGroup) {
                   
                    $isColorOnly = $colorGroup['variations'][0]['isColorOnly'] ?? false;
                    // Création de la variation principale (couleur)
                    $variation = $product->variations()->create([
                        'color_id' => $colorGroup['color']['id'],
                       'price' => $isColorOnly ? $colorGroup['variations'][0]['price'] : 0,
                        'quantity' => $isColorOnly ? $colorGroup['variations'][0]['quantity'] : null,
                    ]);
    
                    // Gestion des images pour cette couleur
                    $colorImageKey = "color_" . $colorGroup['color']['id'] . "_image_";
                    $imageIndex = 0;
                    while ($request->hasFile($colorImageKey . $imageIndex)) {
                        $imagePath = $request->file($colorImageKey . $imageIndex)
                            ->store('product/variations', 'public');
                        $variation->images()->create(['image_path' => $imagePath]);
                        $imageIndex++;
                    }
    
                    // Gestion des sous-variations (tailles/pointures)
                    foreach ($colorGroup['variations'] as $subVariation) {
                        if (isset($subVariation['size'])) {

                            if (!$variation->attributes()->where('attribute_value_id', $subVariation['size']['id'])->exists()) {
                                $variation->attributes()->create([
                                    'attribute_value_id' => $subVariation['size']['id'],
                                    'quantity' => $subVariation['size']['quantity'],
                                    'price' => $subVariation['size']['price']
                                ]);
                            }
                            $isColorOnly = false;
                            break;
                        }
    
                        if (isset($subVariation['shoeSize'])) {
                            if (!$variation->attributes()->where('attribute_value_id', $subVariation['shoeSize']['id'])->exists()) {
                                $variation->attributes()->create([
                                    'attribute_value_id' => $subVariation['shoeSize']['id'],
                                    'quantity' => $subVariation['shoeSize']['quantity'],
                                    'price' => $subVariation['shoeSize']['price']
                                ]);
                            }
                            $isColorOnly = false;
                            break;
                        }

                        
                    }
                }
            }
    
            // Gestion des catégories
            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach(array_map('intval', $request->categories));
            }
    
            if ($request->has('sub_categories') && is_array($request->sub_categories)) {
                $product->categories()->attach(array_map('intval', $request->sub_categories));
            }
    
            DB::commit();
    
            return response()->json(['message' => "Product created successfully"], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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
