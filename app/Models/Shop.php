<?php

namespace App\Models;

use App\Models\Town;
use App\Models\User;
use App\Models\Image;
use Ramsey\Uuid\Uuid;
use App\Models\Product;
use App\Models\Quarter;
use App\Models\Category;
use App\Models\ShopType;
use App\Models\ShopVisit;
use App\Models\ShopReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    protected $keyType = 'string';
    public $incrementing = false;
 protected $primaryKey = 'id';
    public function seller():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function shopType():BelongsTo{
        return $this->belongsTo(ShopType::class);
    }

    public function products():HasMany{

        return $this->hasMany(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }

    public function categories():BelongsToMany{
        return $this->belongsToMany(Category::class);
    }

    public function images():BelongsToMany{
        return $this->belongsToMany(Image::class);
    }

    public function town():BelongsTo{
        return $this->belongsTo(Town::class);
    }

    public function quarter():BelongsTo{
        return $this->belongsTo(Quarter::class);
    }

    public function reviews():HasMany{
        return $this->hasMany(ShopReview::class);
    }

    public function visits()
{
    return $this->hasMany(ShopVisit::class);
}
}
