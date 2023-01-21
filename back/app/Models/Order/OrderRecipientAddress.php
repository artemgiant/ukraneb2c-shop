<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRecipientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_id',
        'phone',
        'city',
        'city_uuid',
        'street',
        'street_uuid',
        'house',
        'flat',
    ];

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = phone_numeral_format($value);
    }
}
