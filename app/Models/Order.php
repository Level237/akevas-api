<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function orderDetails():HasMany{
        return $this->hasMany(OrderDetail::class);
    }

    public function orderVariations():HasMany{
        return $this->hasMany(OrderVariation::class);
    }

    public function processByDelivery():BelongsToMany{
        return $this->belongsToMany(User::class, 'delivery_order', 'order_id', 'user_id')->withPivot('isAccepted');
    }

    public function payment():HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
