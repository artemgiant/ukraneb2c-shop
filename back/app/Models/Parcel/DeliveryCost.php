<?php

namespace App\Models\Parcel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCost extends Model
{
    protected $connection = 'ub2c';

    use HasFactory;
}
