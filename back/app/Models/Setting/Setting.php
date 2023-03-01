<?php

namespace App\Models\Setting;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $connection = 'ub2c';
    protected $guarded = [];

    public $timestamps = false;


}
