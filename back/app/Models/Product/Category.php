<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'ub2c';

    public function categories()
    {
        return $this->hasMany(Category::class, 'category_id', 'id')->orderBy('order')->orderBy('id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class)->with('categories');
    }

    public function childrenRecursive()
    {
        return $this->categories()->with('childrenRecursive');
    }

    public function product_attributes(){
        return $this->belongsToMany(Attribute::class, 'category_attributes', 'category_id', 'attribute_id');
    }
}
