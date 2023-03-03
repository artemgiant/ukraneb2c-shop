<?php


namespace App\Http\Controllers\Api\Settings;


use App\Http\Controllers\Controller;
use App\Models\Setting\Setting;

class SettingsController extends Controller
{
    public function index(){
        $data = Setting::orderBy('order', 'ASC')->where('group','Shop pages')->get();

        return response()->json($data);
    }

    public function page($key){

        $page = Setting::orderBy('order', 'ASC')->where('key','shop-pages.'.$key)->first();

        return response()->json($page);
    }

    public function deliveryPrices(): \Illuminate\Http\JsonResponse
    {

        $total_sum_product = 12123;
        $delivery_type = 'nova_poshta';
        $init_array = ['delivery_cost'=>null];

        $settings = Setting::where('group','Delivery')->get();

//        dd($settings);
//        dump($settings->where('key','delivery.free_'.$delivery_type)->first()->value,$settings->where('key','delivery.cost_'.$delivery_type)->first()->value);

        if ($total_sum_product >= $settings->where('key','delivery.free_'.$delivery_type)->first()->value){
            $init_array['delivery_cost'] = 0;
        } else {
            if (!$init_array['delivery_cost'] && $init_array['delivery_cost'] != "0.00"){
                $init_array['delivery_cost'] = $settings->where('key','delivery.cost_'.$delivery_type)->first()->value;
            }
        }


        return response()->json($settings);
    }

}
