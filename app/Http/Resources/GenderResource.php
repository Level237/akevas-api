<?php

namespace App\Http\Resources;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gender_name' => $this->gender_name,
            'gender_profile' => URL("/storage/".$this->gender_profile),
            'gender_description' => $this->gender_description,
            'categories' => CategoryResource::collection(Category::whereHas('genders', function($query) {
                $query->where('gender_id', $this->id);
            })->where('parent_id', null)->get()),
            'products' => ProductResource::collection(Product::where('product_gender',$this->id)->orWhere('product_gender',4)->take(4)->get()),
            'shops' => ShopResource::collection(Shop::where('shop_gender',$this->id)->inRandomOrder()->orWhere('shop_gender',4)->where('state','=',"1")->take(7)->get()),
        ];
    }
}
