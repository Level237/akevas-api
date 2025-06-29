<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'variation_attribute_id',
        'product_variation_id',
        'variation_quantity',
        'variation_price'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function variationAttribute(): BelongsTo
    {
        return $this->belongsTo(VariationAttribute::class);
    }
}
