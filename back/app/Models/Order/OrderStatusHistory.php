<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_statuses_history';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'user_id'
    ];
}
