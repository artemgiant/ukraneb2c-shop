<?php

namespace App\Models\Order;

use App\Facades\Carrier;
use App\Models\Box\Box;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'ub2c';


    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::created(function (Order $model) {
            $model->createOrderStatusHistory();
        });
    }

    public function status()
    {
        return $this->hasOne('App\Models\Order\OrderStatus', 'id', 'status_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Order\OrderProduct', 'order_id', 'id');
    }


    public function recipient()
    {
        return $this->hasOne('App\Models\Order\OrderRecipient', 'phone', 'phone');
    }

    public function address()
    {
        return $this->hasOne('App\Models\Order\OrderAddress', 'order_id', 'id')->orderByDesc('id');
    }
    public function box()
    {
        return $this->hasOne(Box::class, 'id', 'box_id');
    }

    public function parcel()
    {
        return $this->hasOne('App\Models\Parcel\Parcel', 'order_id', 'id')
            ->whereNotIn('status', ['ERROR', 'DELETED'])
            ->whereNotNull('barcode')
            ->orderByDesc('id');
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = phone_numeral_format($value);
    }

    public function setStatusIdAttribute($value)
    {
        $this->attributes['status_id'] = phone_numeral_format($value);
        $this->createOrderStatusHistory();
    }

    public function createParcel()
    {
        return Carrier::serviceByAlias(setting('ub2c.default_carrier'))->createParcel($this);
    }

    public function createOrderStatusHistory()
    {
        if ($this->id) {
            OrderStatusHistory::create([
                'order_id' => $this->id,
                'order_status_id' => $this->status_id,
                'user_id' => Auth::id(),
            ]);
        }
    }



    public function calculateSum()
    {
        $sum = 0;
        foreach ($this->products as $product) {
            $sum += $product->price->uah * $product->quantity;
        }
        $this->total_sum_product = $sum;

        return $this;
    }

    public function calculateDiscount($discount)
    {
        $delivery = 0;
        $amount = 0;
        $percent = 0;

        if ($discount->type_discount == 'delivery') {
            $delivery += $discount->discount_value;
        } else if ($discount->discount_value) {
            $amount += $discount->discount_value;
        } else if ($discount->discount_percent) {
            $percent += $discount->discount_percent;
        }
        if ($percent) {
            $amount += $this->total_sum_product * ($percent / 100);
        }
        if ($this->total_sum_product <= $amount) {
            $this->discount = $this->total_sum_product;
        } else {
            $this->discount = $amount;
        }
        if ($this->standard_delivery_cost <= $delivery) {
            $this->discount_delivery = $this->standard_delivery_cost;
            $this->delivery_cost = 0;
        } else {
            $this->discount_delivery = $delivery;
            $this->delivery_cost = $this->standard_delivery_cost - $delivery;
        }

        return $this;
    }


    public function getSumToPayAttribute()
    {
        $sum_to_pay = $this->total_sum_product;
        if ($this->discount) {
            $sum_to_pay -= $this->discount;
        }
        if ($this->delivery_cost) {
            $sum_to_pay += $this->delivery_cost;
        }
        return $sum_to_pay;
    }
}
