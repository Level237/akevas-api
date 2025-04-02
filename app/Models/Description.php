<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Description extends Model
{
    use HasFactory;

    public function subscriptions():BelongsToMany{
        return $this->belongsToMany(Subscription::class);
    }
}
