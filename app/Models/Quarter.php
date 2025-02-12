<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Town;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Quarter extends Model
{
    use HasFactory;

    public function town():BelongsTo{
        return $this->belongsTo(Town::class);
    }

    public function shops():HasMany{
        return $this->hasMany(Shop::class);
    }
}
