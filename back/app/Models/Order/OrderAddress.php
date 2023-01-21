<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipient_id',
        'delivery_cost',
        'city',
        'city_uuid',
        'street',
        'street_uuid',
        'house',
        'flat',
        'zip',
        'delivery_type',
        'warehouse_category',
        'warehouse_uuid',
        'warehouse_number',
        'warehouse_short',
        'working_time',
        'is_pos_terminal',
        'is_work',
        'max_weight',
    ];


    public function getAddressStrAttribute()
    {
        $str = "";

        switch ($this->delivery_type) {
            case'meest_apt' :
                $str .= 'Поштомат №'.$this->warehouse_number.' '.$this->street.' '.$this->house;
                break;
            case'nova_poshta' :
            case'meest_point' :
                $str .= 'Відділення №'.$this->warehouse_number.' '.$this->street.' '.$this->house;
                break;

            case 'meest_courier':
                $str .= ucfirst($this->street) . ' ' . $this->house . '/' . $this->flat;
                break;
        }


        return $str;
    }
}
