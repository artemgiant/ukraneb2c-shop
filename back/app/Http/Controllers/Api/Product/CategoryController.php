<?php


namespace App\Http\Controllers\Api\Product;


use App\Http\Controllers\Controller;
use App\Models\Product\Category;

class CategoryController extends Controller
{
    public function tree(){
        $categories =  Category::select('*')->whereNull('category_id')->with('childrenRecursive')->get();

        return $categories;
    }
}
