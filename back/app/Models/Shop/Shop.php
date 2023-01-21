<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Shop extends Model
{
    protected $fillable = [
        "name",
        "class",
        "config"
    ];

    public static function boot()
    {
        parent::boot();
        self::updated(function (Shop $model) {
            self::clearCache($model);
        });

        self::created(function (Shop $model) {
            self::clearCache($model);
        });
    }

    public static function clearCache($model = null)
    {
        if ($model) {
            Cache::forget(self::getCacheKey($model->alias));
            Cache::forget(self::getCacheKey($model->id));
        }
        Cache::forget(self::getCacheKey("all"));
    }

    public static function clearAll()
    {
        $models = Shop::all();
        foreach ($models as $model) {
            self::clearCache($model);
        }
    }

    public static function provider($alias)
    {
        $provider = Cache::rememberForever(self::getCacheKey($alias), function () use ($alias) {
            return Shop::where('alias', $alias)->first();
        });
        if (!$provider) {
            throw  new \Exception("Not find Shop with alias $alias");
        }
        return $provider;
    }

    public static function providerById($id)
    {
        $provider = Cache::rememberForever(self::getCacheKey($id), function () use ($id) {
            return Shop::where('id', $id)->first();
        });
        if (!$provider) {
            throw  new \Exception("Not find Shop with id $id");
        }
        return $provider;
    }

    public static function providers()
    {
        $providers = Cache::rememberForever(self::getCacheKey("all"), function () {
            return Shop::all();
        });
        if (!$providers) {
            throw  new \Exception("Shop empty");
        }
        return $providers;
    }

    public static function getCacheKey($key)
    {
        return "shops_" . '_key_' . $key;
    }

    public function getConfigArrAttribute()
    {
        return empty($this->config) ? (new \stdClass) : json_decode($this->config);
    }
}
