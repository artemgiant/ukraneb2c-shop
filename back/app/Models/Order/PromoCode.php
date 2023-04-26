<?php

namespace App\Models\Order;

use App\Models\Supplier\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'ub2c';

    public function scopeActive($q)
    {
        $q->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereColumn([['activations', '>', 'used_activations']])->orWhereNull('activations');
            })
//            ->where('minimum_cart', '<=', $order_total_sum_product)
            ->where(function ($query) {
                $query->where('start_at', '<=', Carbon::now())->orWhereNull('start_at');
            })
            ->where(function ($query) {
                $query->where('end_at', '>=', Carbon::now())->orWhereNull('end_at');
            });
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'promo_code_suppliers', 'promo_code_id', 'supplier_id');
    }

    public function activation()
    {
        $this->used_activations++;
        $this->save();
    }
}
