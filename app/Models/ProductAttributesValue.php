<?php

namespace App\Models;

use App\Models\Product;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributesValue extends Model
{
    use HasFactory;

    public function product():BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function attributeValue():BelongsTo{
        return $this->belongsTo(AttributeValue::class);
    }
}
