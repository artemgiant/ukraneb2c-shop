<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use HasFactory;


    protected $connection = 'ub2c';

    public function setValueAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
        $this->attributes['value'] = $value;
    }

    public function attribute()
    {
        return $this->hasOne(Attribute::class,'id','attribute_id');
    }
}
