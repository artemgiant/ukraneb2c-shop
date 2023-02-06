<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
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


    public function scopeSearch($q, $request)
    {
        if (!empty($request->get('id'))) {
            $q->where('id', $request->id);
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

}
