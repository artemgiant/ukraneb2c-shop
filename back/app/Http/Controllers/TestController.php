<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Models\Setting\Setting;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class TestController extends BaseController
{
    public function index()
    {
//        $order = \stdClass::class;
//
        $total_sum_product = 12123;
        $delivery_type = 'nova_poshta';
        $init_array = ['delivery_cost'=>null];

        $settings = Setting::where('group','Delivery')->get();

dump($settings->where('key','delivery.free_'.$delivery_type)->first()->value,$settings->where('key','delivery.cost_'.$delivery_type)->first()->value);

            if ($total_sum_product >= $settings->where('key','delivery.free_'.$delivery_type)->first()->value){
                $init_array['delivery_cost'] = 0;
            } else {
                if (!$init_array['delivery_cost'] && $init_array['delivery_cost'] != "0.00"){
                    $init_array['delivery_cost'] = $settings->where('key','delivery.cost_'.$delivery_type)->first()->value;
                }
            }

        dd($init_array['delivery_cost'],$settings);

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        return 'ok';

        $product = Product::first();

        dump(Config::get('comments.model'));

        $comments = $product->comments;
        dump($comments,$product);
        $user = User::first();
        $comment = new \Laravelista\Comments\Comment;
        $comment->commenter()->associate($user);
        $comment->commentable()->associate($product);
        $comment->comment = 'Test comment';
        $comment->approved = true;
        $comment->stars = 4;
        $comment->save();

        $model = $product;

        return view('home',compact('model'));
    }
}
