<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Image;
use Ramsey\Uuid\Uuid;
use App\Models\Category;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'product_description',
        'product_url',
        'product_price',
        'product_quantity',
    ];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public function images():BelongsToMany{
        return $this->belongsToMany(Image::class);
    }
    public function shop():BelongsTo{
        return $this->belongsTo(Shop::class);
    }
    public function attributes():BelongsToMany{
        return $this->belongsToMany(AttributeValue::class);
    }

    public function categories():BelongsToMany{
        return $this->belongsToMany(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }

}
