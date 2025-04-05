<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Description extends Model
{
    use HasFactory;
    protected $fillable = [
        'description_name',
    ];
    public function subscriptions():BelongsToMany{
        return $this->belongsToMany(Subscription::class, 'description_subscription');
    }

    
}
