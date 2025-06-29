<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable=[
        'payment_type',
        'price',
        'user_id',
        'transaction_id',
        'transaction_ref',
        'payment_of',
        'order_id',
        'subscription_id',
        'status'
    ];


    public function order(){

        return $this->belongsTo(Order::class);
    }
    public function user(){

        return $this->belongsTo(User::class);
    }
}
