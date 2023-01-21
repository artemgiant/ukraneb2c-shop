<?php


namespace App\Http\Controllers\Api\Product;


use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends  Controller
{
    public function index(Request $request)
    {

        if (!$request->length)
            $request->request->set('length', 10);

        $pagination = Product::orderByDesc('id')
            ->whereNotNull('images')
            ->search($request)
            ->paginate($request->length, [(new Product())->getTable() . '.*'], 'page', ($request->start / $request->length) + 1);


        $totalLength = $pagination->total();
        $products = ProductResource::collection($pagination);

        $products->toJson();
        return response()->json(['products' => $products, 'totalLength' => $totalLength]);
    }
}
