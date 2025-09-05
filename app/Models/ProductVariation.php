<?php

namespace App\Models;

use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\VariationImage;
use App\Models\WholeSalePrice;
use App\Models\VariationAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'color_id', 'price','quantity'];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, 'color_id');
    }

    public function attributesVariation(): HasMany
    {
        return $this->hasMany(VariationAttribute::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(VariationImage::class);
    }

    public function wholesalePrices()
    {
        return $this->morphMany(WholeSalePrice::class, 'priceable');
    }
    
}
