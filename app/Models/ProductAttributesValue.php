<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Product;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductAttributesValue extends Model
{
    use HasFactory;
    protected $fillable=['product_id','attribute_value_id','price','variant_name'];

    public function product():BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function attributeValue():BelongsTo{
        return $this->belongsTo(AttributeValue::class);
    }

    public function images():BelongsToMany{
        return $this->belongsToMany(Image::class, 'product_attributes_value_image');
    }
}


