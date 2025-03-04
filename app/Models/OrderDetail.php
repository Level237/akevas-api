<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
class OrderDetail extends Model
{
    use HasFactory;

    public function order():BelongsTo{
        return $this->belongsTo(Order::class);
    }

    public function product():BelongsTo{
        return $this->belongsTo(Product::class);
    }
}
