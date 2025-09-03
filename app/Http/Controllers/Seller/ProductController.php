<?php

namespace App\Http\Controllers\Seller;

use Exception;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\WholeSalePrice;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateProductUrlJob;
use Illuminate\Support\Facades\Log;
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
            $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')
            ->where("is_trashed",0)
            ->get();

            return ProductResource::collection($products);
        }

        public function productListOfTrash(){
            $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
            $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')
            ->where("is_trashed",1)
            ->get();

            return ProductResource::collection($products);
        }

        /**
         * Store a newly created resource in storage.
         */
   public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Log de la requête entrante pour le débogage
            Log::info('Product store request received', ['request_all' => $request->all()]);
            Log::info('Files in request', ['request_files' => $request->files->all()]);


            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();

            if (!$shop) {
                throw new \Exception("Shop not found for the authenticated user.");
            }

            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }

            $product = new Product;
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->shop_id = $shop->id;
            $product->type = $request->type == 'simple' ? 0 : 1;
            $product->product_gender = $request->product_gender;
            $product->whatsapp_number = $request->whatsapp_number;
            $product->product_residence = $request->product_residence;
            $product->status = 0; // Default status


            if ($product->type == 0) { // Simple product
                $product->product_price = $request->product_price;
                $product->product_quantity = $request->product_quantity;
            }

            if ($request->hasFile('product_profile')) {
                $product->product_profile = $request->file('product_profile')->store('product/profile', 'public');
                Log::info('Product profile image stored', ['path' => $product->product_profile]);
            }

            $product->save();
            Log::info('Product saved', ['product_id' => $product->id]);

            GenerateProductUrlJob::dispatch($product->id);


            if ($request->is_wholesale == "1") {
                $product->is_wholesale = true;
                if ($request->is_only_wholesale == "1") {
                    $product->is_only_wholesale = true;
                }
                $product->save();
                if ($request->has('wholesale_prices')) {
                    $wholesalePrices = json_decode($request->wholesale_prices);
                    if ($wholesalePrices) {
                        foreach ($wholesalePrices as $wholeSalePrice) {
                            $newWholeSalePrice = new WholeSalePrice;
                            $newWholeSalePrice->min_quantity = $wholeSalePrice->min_quantity;
                            $newWholeSalePrice->wholesale_price = $wholeSalePrice->wholesale_price;
                            $newWholeSalePrice->product_id = $product->id;
                            $newWholeSalePrice->save();
                            Log::info('Product wholesale price saved', ['price_id' => $newWholeSalePrice->id]);
                        }
                    }
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product/images', 'public');
                    $product->images()->create(['image_path' => $imagePath]);
                    Log::info('Product main image stored', ['path' => $imagePath]);
                }
            }

            if ($product->type == 1 && $request->filled('variations')) {
                $variationsData = json_decode($request->variations, true);
                Log::info('Decoded variations data', ['variations' => $variationsData]);

                foreach ($variationsData as $colorGroup) {
                    // Création de la variation principale (couleur)
                    $variation = $product->variations()->create([
                        'color_id' => $colorGroup['color']['id'],
                        'price' => $colorGroup['price'] ?? 0,
                        'quantity' => $colorGroup['quantity'] ?? null,
                    ]);
                    Log::info('ProductVariation (color) created', ['variation_id' => $variation->id, 'color_id' => $colorGroup['color']['id']]);

                    // Gestion des images pour cette couleur
                    $colorImageKeyPrefix = "color_" . $colorGroup['color']['id'] . "_image_";
                    $imageIndex = 0;
                    while ($request->hasFile($colorImageKeyPrefix . $imageIndex)) {
                        $imageFile = $request->file($colorImageKeyPrefix . $imageIndex);
                        $imagePath = $imageFile->store('product/variations', 'public');
                        $variation->images()->create(['image_path' => $imagePath]);
                        Log::info('Variation image stored', ['color_id' => $colorGroup['color']['id'], 'image_index' => $imageIndex, 'path' => $imagePath]);
                        $imageIndex++;
                    }

                    // Gestion des sous-variations (tailles/pointures)
                    if (isset($colorGroup['sizes']) && is_array($colorGroup['sizes'])) {
                        Log::info('Processing sizes for color group', ['color_id' => $colorGroup['color']['id'], 'sizes_data' => $colorGroup['sizes']]);
                        foreach ($colorGroup['sizes'] as $attributeValue) {
                            if (!$variation->attributesVariation()->where('attribute_value_id', $attributeValue['id'])->exists()) {
                                $attrVariation = $variation->attributesVariation()->create([
                                    'attribute_value_id' => $attributeValue['id'],
                                    'quantity' => $attributeValue['quantity'],
                                    'price' => $attributeValue['price'],
                                ]);
                                Log::info('ProductAttributeVariation (size) created', ['id' => $attrVariation->id, 'attribute_value_id' => $attributeValue['id']]);

                                if (isset($attributeValue['is_wholesale']) && $attributeValue['is_wholesale'] && isset($attributeValue['wholesale_prices']) && is_array($attributeValue['wholesale_prices'])) {
                                    foreach ($attributeValue['wholesale_prices'] as $wholesalePriceData) {
                                        $attrVariation->wholesalePrices()->create([
                                            'min_quantity' => $wholesalePriceData['min_quantity'],
                                            'wholesale_price' => $wholesalePriceData['wholesale_price'],
                                        ]);
                                        Log::info('Attribute variation wholesale price saved', ['attr_variation_id' => $attrVariation->id, 'min_quantity' => $wholesalePriceData['min_quantity']]);
                                    }
                                }
                            }
                        }
                    }

                    if (isset($colorGroup['shoeSizes']) && is_array($colorGroup['shoeSizes'])) {
                        Log::info('Processing shoe sizes for color group', ['color_id' => $colorGroup['color']['id'], 'shoe_sizes_data' => $colorGroup['shoeSizes']]);
                        foreach ($colorGroup['shoeSizes'] as $attributeValue) {
                            if (!$variation->attributesVariation()->where('attribute_value_id', $attributeValue['id'])->exists()) {
                                $attrVariation = $variation->attributesVariation()->create([
                                    'attribute_value_id' => $attributeValue['id'],
                                    'quantity' => $attributeValue['quantity'],
                                    'price' => $attributeValue['price'],
                                ]);
                                Log::info('ProductAttributeVariation (shoe size) created', ['id' => $attrVariation->id, 'attribute_value_id' => $attributeValue['id']]);

                                if (isset($attributeValue['is_wholesale']) && $attributeValue['is_wholesale'] && isset($attributeValue['wholesale_prices']) && is_array($attributeValue['wholesale_prices'])) {
                                    foreach ($attributeValue['wholesale_prices'] as $wholesalePriceData) {
                                        $attrVariation->wholesalePrices()->create([
                                            'min_quantity' => $wholesalePriceData['min_quantity'],
                                            'wholesale_price' => $wholesalePriceData['wholesale_price'],
                                        ]);
                                        Log::info('Attribute variation wholesale price saved', ['attr_variation_id' => $attrVariation->id, 'min_quantity' => $wholesalePriceData['min_quantity']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach(array_map('intval', $request->categories));
                Log::info('Categories attached', ['categories' => $request->categories]);
            }

            if ($request->has('sub_categories') && is_array($request->sub_categories)) {
                $product->categories()->attach(array_map('intval', $request->sub_categories));
                Log::info('Sub-categories attached', ['sub_categories' => $request->sub_categories]);
            }

            DB::commit();
            Log::info('Product creation transaction committed successfully for product', ['product_id' => $product->id]);

            return response()->json(['message' => "Product created successfully"], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_all' => $request->all(), // Log all request data on failure
                'request_files' => $request->files->all(), // Log all file data on failure
            ]);
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

        public function putInTrash($id){
            $product=Product::find($id);
            $product->is_trashed=1;
            
            $product->save();
            return response()->json(['message' => 'Product put in trash successfully']);
        }

        public function restoreProduct($id){
            $product=Product::find($id);
            $product->is_trashed=0;
            $product->save();
            return response()->json(['message' => 'Product restore successfully']);
        }
    }
