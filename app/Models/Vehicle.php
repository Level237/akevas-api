<?php

namespace App\Models;

use App\Models\Town;
use App\Models\User;
use App\Models\Quarter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    use HasFactory;

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function quarters():BelongsToMany{
        return $this->belongsToMany(Quarter::class,'vehicle_quarter');
    }
}
