<?php

namespace App\Models;

use App\Models\User;
use App\Models\ShopType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'shop_key',
        'shop_description',
        'user_id',
        'shop_type_id',
    ];

    public function seller():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function shopType():BelongsTo{
        return $this->belongsTo(ShopType::class);
    }
}
