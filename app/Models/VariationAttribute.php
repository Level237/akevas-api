<?php

namespace App\Models;

use App\Models\AttributeValue;
use App\Models\WholeSalePrice;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariationAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variation_id',
        'attribute_value_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }

     public function wholesalePrices()
    {
        return $this->morphMany(WholeSalePrice::class, 'priceable');
    }
}
