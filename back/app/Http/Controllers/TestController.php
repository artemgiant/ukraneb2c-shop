<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
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
