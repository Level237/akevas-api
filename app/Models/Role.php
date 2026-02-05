<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name'
    ];

    public function users(): HasMany
    {
        // Ici, Laravel cherchera bien 'role_id' dans la table 'users'
        return $this->hasMany(User::class, 'role_id');
    }
}
