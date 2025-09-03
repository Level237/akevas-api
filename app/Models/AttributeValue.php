<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValueGroup;
use App\Models\ProductAttributesValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    use HasFactory;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes_values')
            ->withPivot('price', 'variant_name');
    }

    public function attributes()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productAttributesValues():HasMany{
        return $this->hasMany(ProductAttributesValue::class);
    }

    public function attributeValueGroup(){
        return $this->belongsTo(AttributeValueGroup::class);
    }
}
