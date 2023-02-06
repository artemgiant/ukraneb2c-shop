<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $connection = 'ub2c';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'total_sum',
        'price_from_market',
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
        'name' => 'object',
        'name_cn23' => 'object',
        'price' => 'object',
        'total_sum' => 'object',
        'price_from_market' => 'object',
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order\Order', 'order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier\Supplier', 'supplier_id', 'id');
    }

    public function sender_address()
    {
        return $this->hasOne('App\Models\SenderAddress\SenderAddress', 'supplier_id', 'supplier_id');
    }

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateSum($value);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = is_string($value) ? $value : json_encode($value);
        $this->calculateSum(null, $value);
    }

    public function getTotalWeightKgAttribute()
    {
        if ($this->quantity) {
            return $this->quantity * $this->weight_kg;
        }
        return $this->weight_kg;
    }

    public function getPriceEurAttribute()
    {
        return $this->price->eur;
    }

    public function getTotalSumEurAttribute()
    {
        return $this->total_sum->eur;
    }

    public function calculateSum($quantity = null, $price = null)
    {
        if (!$quantity) {
            $quantity = $this->quantity;
        }
        if (!$price) {
            $price = $this->price;
        }
        if ($price && $quantity) {
            $total_sum = new \stdClass();
            foreach ($price as $currency => $value) {
                $total_sum->$currency = $value * $quantity;
            }
            $this->total_sum = $total_sum;
        }
        return $this;
    }

    public static function copyFromProduct(Product $product_local)
    {
        return [
            'product_id' => $product_local->id,
            'supplier_id' => $product_local->supplier_id,
            'brand_id' => $product_local->brand_id,
            'local_code' => $product_local->local_code,
            'code' => $product_local->code,
            'ean' => $product_local->ean,
            'name' => json_decode($product_local->name),
            'name_cn23' => json_decode($product_local->name_cn23),
            'price' => json_decode($product_local->price),
            'in_stock' => $product_local->in_stock,
            'hc_code' => $product_local->hc_code,
            'country_of_origin' => $product_local->country_of_origin,
            'weight_kg' => $product_local->weight_kg,
            'images' => $product_local->images
        ];

    }
}
