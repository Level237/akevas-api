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
public function getParentsAttribute()
{
    $parents = collect([]);

    $parent = $this->parents;

    while(!is_null($parent)) {
        $parents->push($parent);
        $parent = $parent->parents;
    }

    return $parents;
}
    public function shops():BelongsToMany{
        return $this->belongsToMany(Shop::class);
    }

    public function parent():BelongsToMany{
        return $this->belongsToMany(Category::class, 'category_parent', 'category_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(Category::class, 'category_parent', 'parent_id', 'category_id');
    }

   
}
