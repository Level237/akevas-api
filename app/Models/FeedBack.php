<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedBack extends Model
{
    use HasFactory;

    protected $table = "feedbacks";
    public function user(){
        return $this->belongsTo(User::class);
    }
}
