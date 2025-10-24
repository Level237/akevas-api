<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Gender;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable =[
        'category_name',
        'gender_id'
    ];
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

    public function genders()
    {
        return $this->belongsToMany(Gender::class, 'category_gender');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

   
}
