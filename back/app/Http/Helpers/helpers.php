<?php


use Illuminate\Support\Facades\Config;

//if (!function_exists('menu')) {
//
//    function menu($menuName, $type = null, array $options = [])
//    {
//        return App\Menu::display($menuName, $type, $options);
//    }
//}


if (!function_exists('pr')) {

    function pr($data, $die = '')
    {
        echo "<pre>" . print_r($data, true) . "</pre>";
        if ($die) die;
    }
}
if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('cvf_convert_object_to_array')) {
    function cvf_convert_object_to_array($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        } else {
            return $data;
        }
    }
}

if (!function_exists('sort_nested_arrays')) {
    function sort_nested_arrays($array, $args = array('votes' => 'desc'))
    {
        usort($array, function ($a, $b) use ($args) {
            $res = 0;

            $a = (object)$a;
            $b = (object)$b;

            foreach ($args as $k => $v) {
                if ($a->$k == $b->$k) continue;

                $res = ($a->$k < $b->$k) ? -1 : 1;
                if ($v == 'desc') $res = -$res;
                break;
            }

            return $res;
        });

        return $array;
    }
}

if (!function_exists('domain_route')) {
    function domain_route($domain, $route_name, $parameters = [])
    {
        $domain_first = substr($domain, 0, 4);
        if ($domain_first !== "http") {//если $domain не начинается с http значит это subdomain и нужно добавить протокол и основной домен
            $schema = "http";

            if ($domain == 'pre_lpp' || !in_array($domain_first, ['pre_', 'test'])) {
                $schema = "https";
            }

            $domain = $schema . "://" . $domain . "." . Config::get('out-notification.MAIN_DOMAIN');

        }
        return trim($domain, '/') . route($route_name, $parameters, false);
    }
}

if (!function_exists('update_env')) {
    function update_env($key, $new_value, $delim='')
    {
        $path = base_path('.env');
        // get old value from current env
        $old_value = env($key);

        // was there any change?
        if ($old_value === $new_value) {
            return;
        }

        // rewrite file content with changed data
        if (file_exists($path)) {
            // replace current value with new value
            file_put_contents(
                $path, str_replace(
                    $key.'='.$delim.$old_value.$delim,
                    $key.'='.$delim.$new_value.$delim,
                    file_get_contents($path)
                )
            );
        }
    }
}

if (!function_exists('asset_v')) {
    function asset_v($path, $secure = null)
    {
        return asset($path, $secure) . '?v=' . config('app.file_version');
    }
}

if (!function_exists('phone_numeral_format')) {
    function phone_numeral_format($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}

if (!function_exists('exchange')) {
    function exchange($value, $currency_from = 'eur', $currency_to = 'uah')
    {
        if ($currency_from == 'eur') {
            if ($currency_to == 'uah') {
                return round($value * floatval(setting('ub2c.ex_eur_uah')), 2);
            }
        }
        if ($currency_from == 'uah') {
            if ($currency_to == 'eur') {
                return round($value / floatval(setting('ub2c.ex_eur_uah')), 2);
            }
        }
        throw new Exception("Exchange currency unknown", 500);
    }
}


