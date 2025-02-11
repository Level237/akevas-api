<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    public function products():BelongsToMany{
        return $this->belongsToMany(Product::class);
    }

    public function shops():BelongsToMany{
        return $this->belongsToMany(Shop::class);
    }
}
