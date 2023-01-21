<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function sender_address()
    {
        return $this->hasOne('App\Models\SenderAddress\SenderAddress', 'supplier_id', 'id');
    }
}
