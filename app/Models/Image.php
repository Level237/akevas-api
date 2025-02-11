<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Shop;
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
}
