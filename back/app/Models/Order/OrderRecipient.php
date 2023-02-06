<?php

namespace App\Models\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRecipient extends Model
{
    use HasFactory;

    protected $connection = 'ub2c';

    protected $fillable = ['phone', 'name', 'email'];

    protected $casts = [
        'name_history' => 'array',
        'email_history' => 'array',
    ];

    public function addresses()
    {
        return $this->hasMany('App\Models\Order\OrderRecipientAddress', 'recipient_id', 'id');
    }

    public function address()
    {
        return $this->hasOne('App\Models\Order\OrderRecipientAddress', 'recipient_id', 'id')->orderByDesc('id');
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = phone_numeral_format($value);
    }

    public function setEmailAttribute($value)
    {
        $this->saveToHistory("email_history", $this->email, $value);
        $this->attributes['email'] = $value;
    }

    public function setNameAttribute($value)
    {
        $this->saveToHistory("name_history", $this->name, $value);
        $this->attributes['name'] = str_replace(['ʼ','"'], "'", $value);
    }

    public function saveToHistory($to, $old_value, $value)
    {
        if ($old_value != $value) {
            $history = $this->$to;
            if (empty($history)) {
                $history = [];
            }
            $history_values = [];
            foreach ($history as $item) {
                if (isset($item['value'])) {
                    $history_values[] = $item['value'];
                }
            }
            if (!in_array($value, $history_values)) {
                $history[] = ['date' => Carbon::now()->toDateTimeString(), 'value' => $value];
                $this->$to = $history;
            }

        }

    }
}
