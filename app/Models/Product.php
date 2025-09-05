<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Image;
use Ramsey\Uuid\Uuid;
use App\Models\Review;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\AttributeValue;
use App\Models\WholeSalePrice;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_wholesale',
        'is_only_wholesale',
        "is_trashed"
    ];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attributes_values')
            ->withPivot('price', 'variant_name');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }
    public function orderDetails():HasMany{
        return $this->hasMany(OrderDetail::class);
    }
    public function reviews():HasMany{
        return $this->hasMany(Review::class);
    }
    public function productAttributesValues():HasMany{
        return $this->hasMany(ProductAttributesValue::class);
    }
    public function variations(){
        return $this->hasMany(ProductVariation::class);
    }

    public function orderVariations(){
        return $this->hasManyThrough(OrderVariation::class, ProductVariation::class);
    }

    public function getVariations()
{
    // Récupère toutes les variations du produit
    return $this->variations->map(function($variation) {
        // Vérifie si c'est une variation couleur uniquement
        $isColorOnly = collect($variation->attributesVariation)->isEmpty() && $variation->quantity != null;
        // Images de la variation couleur
        $images = $variation->images->map(function($img) {
            return URL("/storage/" . $img->image_path);
        });

        $base = [
            "id" => $variation->id,
            "color" => [
                "id" => $variation->color->id,
                "name" => $variation->color->value,
                "hex" => $variation->color->hex_color,
            ],
            "images" => $images,
            "isColorOnly" => $isColorOnly,
        ];

        if ($isColorOnly) {
            // Cas couleur uniquement
            $base["quantity"] = $variation->quantity;
            $base["price"] = $variation->price;
        } else {
            // Cas couleur + attributs (taille/pointure)
            $base["attributes"] = $variation->attributesVariation->map(function($attr) {
                return [
                    "id" => $attr->id,
                    "name" => $attr->attributeValue->attribute->name ?? null,
                    "value" => $attr->attributeValue->value ?? null,
                    "group"=>$attr->attributeValue->attributeValueGroup->label ?? null,
                    "label"=>$attr->attributeValue->label ?? null,
                    "quantity" => $attr->quantity ?? null,
                    "price" => $attr->price ?? null,
                ];
            });
        }

        return $base;
    });
}

    public function wholesalePrices()
    {
        return $this->morphMany(WholeSalePrice::class, 'priceable');
    }
}
