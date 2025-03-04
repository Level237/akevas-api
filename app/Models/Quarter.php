<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\Town;
use App\Models\Quarter;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quarter extends Model
{
    use HasFactory;

    public function vehicle():BelongsToMany{
        return $this->belongsToMany(Vehicle::class,'vehicle_quarter');
    }

    public function shops():HasMany{
        return $this->hasMany(Shop::class);
    }

    public function town():BelongsTo{
        return $this->belongsTo(Town::class);
    }
}
