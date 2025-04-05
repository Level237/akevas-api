<?php

namespace App\Models;

use App\Models\Description;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
