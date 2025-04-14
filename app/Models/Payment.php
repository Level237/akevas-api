<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
