<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'discount_value', 'type_discount', 'user_id'];
}
