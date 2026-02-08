<?php

namespace App\Models;

use App\Models\Description;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Shop;
use App\Models\SubscriptionUser;
class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_name',
        'subscription_price',
        'subscription_description',

    ];

    public function descriptions()
    {
        return $this->belongsToMany(Description::class, 'description_subscription');
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function subscriptionUsers(): HasMany
    {
        return $this->hasMany(SubscriptionUser::class);
    }

}
