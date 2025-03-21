<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductAttributesValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Image extends Model
{
    use HasFactory;

    protected $fillable=[
        'image_path'
    ];

    public function products():BelongsToMany{
        return $this->belongsToMany(Product::class);
    }

    public function shops():BelongsToMany{
        return $this->belongsToMany(Shop::class);
    }

    public function productAttributesValues():BelongsToMany{
        return $this->belongsToMany(ProductAttributesValue::class, 'product_attributes_value_image');
    }
}
