<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Quarter;
use App\Models\Shop;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Town extends Model
{
    use HasFactory;

    public function quarters():HasMany{
        return $this->hasMany(Quarter::class);
    }

    public function shops():HasMany{
        return $this->hasMany(Shop::class);
    }

    public function vehicles():BelongsToMany{
        return $this->belongsToMany(Vehicle::class);
    }
}
