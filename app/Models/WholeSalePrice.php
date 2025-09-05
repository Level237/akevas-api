<?php

namespace App\Models;

use App\Models\WholeSalePrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WholeSalePrice extends Model
{
    use HasFactory;

     protected $fillable = [
        'min_quantity',
        'wholesale_price',
        'priceable_id',
        'priceable_type',
    ];

      public function priceable()
    {
        return $this->morphTo();
    }
}
