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

    protected $fillable = ['shop_id',
        'shop_order_id',
        'shop_user_id',
        'order_number',
        'phone',
        'email',
        'order_date',
        'status_id',
        'total_sum',
        'total_sum_without_discount',
        'discount',
        'coupon_code',
        'payment_type',
        'payed',
        'delivery_type_id',
        'cost_of_delivery',
        'discount_delivery'
        ];

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

    public function calculateSum()
    {
        $sum = 0;
        foreach ($this->products as $product) {
            $sum += $product->price->uah * $product->quantity;
        }
        $this->total_sum_product = $sum;
        return $this;
    }

}
