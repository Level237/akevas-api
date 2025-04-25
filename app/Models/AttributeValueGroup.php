<?php

namespace App\Models;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValueGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'label',
    ];  

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
