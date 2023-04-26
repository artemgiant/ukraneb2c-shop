<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderDiscount extends Model
{
    use HasFactory;

    protected $connection = 'ub2c';


    protected $guarded = [];
}
