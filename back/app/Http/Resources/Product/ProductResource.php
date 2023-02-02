<?php

namespace App\Http\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data     = $this->getAttributes();

        $result = Arr::only($data,['id','weight_kg','images','price','name','description_long','description_short']);
        $result['image_main'] = null;
        $result['price'] = json_decode( $result['price'])->uah;
        $name =
            json_decode($result['name'])->ukr;

        if (empty($name)) {
            $name = json_decode($result['name'])->cze;
        }
        $result['name'] = $name;

        if(!empty($result['images'])){
            $result['images'] =  json_decode($result['images'],JSON_UNESCAPED_UNICODE);
            $result['image_main'] =  Arr::first( $result['images']);
        }

       return $result;

    }
}
