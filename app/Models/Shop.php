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
        'shop_description',
        'product_type',
        'shop_gender',           // Assurez-vous que le nom du champ est bien 'gender' et non 'shop_gender'         // Si vous stockez le nom du quartier
        'town_id',          // Si vous utilisez les IDs
        'quarter_id',       // Si vous utilisez les IDs
        'shop_profile',     // Pour l'URL/chemin de l'image de profil
        'user_id',          // Important si c'est défini lors de la création
        // Ajoutez ici toutes les colonnes qui pourraient être mises à jour
    ];

    protected $keyType = 'string';
    public $incrementing = false;
 protected $primaryKey = 'id';
 
    public function user():BelongsTo{
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
        return $this->belongsToMany(Category::class,'category_shop');
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
