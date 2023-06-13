<?php


namespace App\Http\Controllers\Api\Product;


use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function tree(Request $request){
        $categories = Category::select('*')
            ->whereHas('shops', function ($q) use ($request) {
                $q->where('alias', $request->get('shop-alias'));
            })
            ->whereNull('category_id')->with('childrenRecursive')->get();

        return $categories;
    }
}
