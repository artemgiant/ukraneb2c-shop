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

}
