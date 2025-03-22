<?php

namespace App\Http\Controllers\Seller;

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

            $product = new Product;
            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();
            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }
            $product=Product::find("f3e5b8ae-7446-46a3-99c1-c76f13833f0f");
           
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
                        try {
                            DB::beginTransaction();

                            $variantNameMap = [];
                            $allAttributesData = [];
                            $variants = json_decode($request->variants, true);

                            foreach ($variants as $index => $variant) {
                                // Vérifier si la variante existe déjà pour ce produit
                                

                               
                                $variantNameMap[$variant['variant_name']] = true;

                                if (!empty($variant['attribute_value_id'])) {
                                    $image_paths = [];
                                    
                                    // Vérification et traitement des images
                                    $variantImageKey = "variant_images_{$index}_0";
                                    if ($request->hasFile($variantImageKey)) {
                                        $imageIndex = 0;
                                        
                                        while ($request->hasFile("variant_images_{$index}_{$imageIndex}")) {
                                            $image = $request->file("variant_images_{$index}_{$imageIndex}");
                                            $image_paths[] = $image->store("product/variants", "public");
                                            $imageIndex++;
                                        }
                                    }

                                    foreach ($variant['attribute_value_id'] as $attributeId) {
                                        // Vérifier si la combinaison variant_name/attribute_value_id existe déjà
                                        $existingAttributeVariant = ProductAttributesValue::where('variant_name', $variant['variant_name'])
                                            ->where('product_id', $product->id)
                                            ->where('attribute_value_id', $attributeId)
                                            ->exists();

                                        if ($existingAttributeVariant) {
                                            throw new \Exception('This combination of variant name and attribute already exists');
                                        }

                                        $allAttributesData[] = [
                                            'attribute_value_id' => $attributeId,
                                            'price' => (string)$variant['price'],
                                            'image_paths' => $image_paths,
                                            'variant_name' => $variant['variant_name']
                                        ];
                                    }
                                }
                            }

                            // Création des nouvelles relations
                            foreach ($allAttributesData as $data) {
                                // Création du ProductAttributesValue
                                $existingVariant = ProductAttributesValue::where('variant_name', $data['variant_name'])
                                    ->where('product_id', $product->id)
                                    ->exists();
                                if($existingVariant){

                                }else{
                                    $productAttributeValue = ProductAttributesValue::create([
                                        'product_id' => $product->id,
                                        'attribute_value_id' => $data['attribute_value_id'],
                                        'price' => $data['price'],
                                        'variant_name' => $data['variant_name']
                                    ]);
                                }
                                
                                
                                // Création et association des images
                                foreach ($data['image_paths'] as $image_path) {
                                    $image = Image::create(['image_path' => $image_path]);
                                    DB::table('product_attributes_value_image')->insert([
                                        'attributes_id' => $productAttributeValue->id,
                                        'image_id' => $image->id,
                                    ]);
                                }
                            }

                            DB::commit();

                            return response()->json([
                                'success' => true,
                                'message' => 'Product variants created successfully'
                            ]);

                        } catch (\Exception $e) {
                            DB::rollBack();
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'Error creating variants: ' . $e->getMessage()
                            ], 422);
                        }
                    }

                    return response()->json(['message' => "Product created successfully"], 201);
                }
            


            //return response()->json(['message' => "Product created successfully"], 201);
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
