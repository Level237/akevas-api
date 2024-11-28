<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Category;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    public function images():BelongsToMany{
        return $this->belongsToMany(Image::class);
    }

    public function attributes():BelongsToMany{
        return $this->belongsToMany(AttributeValue::class);
    }

    public function categories():BelongsToMany{
        return $this->belongsToMany(Category::class);
    }
}
