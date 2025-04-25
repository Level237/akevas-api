<?php

namespace App\Models;

use App\Models\AttributeValue;
use App\Models\AttributeValueGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    use HasFactory;

    public function attributesValues(): HasMany
    {

        return $this->hasMany(AttributeValue::class);
    }

    public function attributeValueGroups(): HasMany
    {
        return $this->hasMany(AttributeValueGroup::class);
    }
}
