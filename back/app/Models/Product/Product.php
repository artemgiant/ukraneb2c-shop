<?php

namespace App\Models\Product;

use App\Models\Comment\Comment;
use App\Models\Shop\Shop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravelista\Comments\Commentable;

class Product extends Model
{
    use HasFactory, Commentable;

    protected $connection = 'ub2c';

    protected $fillable = [
        'supplier_id',
        'brand_id',
        'local_code',
        'code',
        'ean',
        'name',
        'name_cn23',
        'price',
        'in_stock',
        'hc_code',
        'country_of_origin',
        'weight_kg',
        'images'
    ];

    protected $casts = [
//        'name' => 'object',
//        'name_cn23' => 'object',
//        'price' => 'object',
    ];

//    public function getNameBrowseAttribute(){
//        $text = "";
//        foreach (json_decode($this->name) as $key => $item) {
//            $text .= $key . ":" . $item . ";";
//        }
//        return $text;
//    }

    public function setWeightKgAttribute($value)
    {
        $this->attributes['weight_kg'] = str_replace([','], ".", $value);
    }

    public function shops(){
        return $this->belongsToMany(Shop::class,'shop_products',);
    }

    public function setCodeAttribute($value)
    {
        $local_code = "";
        $supplier = $this->supplier;
        if ($supplier) {
            $local_code = $supplier->local_code;
        }
        if ($local_code) {
            $this->attributes['local_code'] = $local_code . "-" . $value;
        }
        $this->attributes['code'] = $value;
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier\Supplier', 'supplier_id', 'id');
    }


    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }


    public function scopeSearch($q, $request)
    {
        if (!empty($request->get('id'))) {
            $q->where('id', $request->id);
        }


        if(!empty($request->get('category'))){
            $q->where('category_id',$request->get('category'));
        }
        if(!empty($request->get('search'))){
            $q
                ->whereRaw("UPPER(`name`) LIKE '%".mb_strtoupper($request->get('search'))."%'");
        }

        if (($request->has('value') && !empty($request->get('value')))) {
            foreach ($request->all() as $key => $value) {
                if ($key == 'value') {
                    if ($value) {
//                        $filters = preg_split("/[\s,]+/", trim($value));
                        $filters = '%' . trim($value) . '%';
                        $q->where(function ($query) use ($filters, $key) {
                            $query->orWhere('local_code', 'like', $filters);
                            $query->orWhere('name', 'like', $filters);
                        });
                    }
                }
            }
        }

        return $q;
    }



    /**
     * Returns all comments for this model.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
