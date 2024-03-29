<?php


namespace App\Http\Controllers\Api\Comment;


use App\Models\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Laravelista\Comments\Comment;

class CommentController
{


    /**
     * Creates a new comment for given model.
     */
    public function store(Request $request)
    {
//        // If guest commenting is turned off, authorize this action.
//        if (Config::get('comments.guest_commenting') == false) {
//            Gate::authorize('create-comment', Comment::class);
//        }
//dd($request->all());
        // Define guest rules if user is not logged in.
        if (!Auth::check()) {
            $guest_rules = [
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        // Merge guest rules, if any, with normal validation rules.
        Validator::make($request->all(), array_merge($guest_rules ?? [], [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|string|min:1',
            'message' => 'required|string'
        ]))->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);

        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->stars = $request->stars;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->save();

        return  response()->json($comment);
    }

    public function get(Request $request){

        if (!$request->length)
            $request->request->set('length', 3);

        Validator::make($request->all(), [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|string|min:1',
        ])->validate();

     $comments =   Comment::where('commentable_type','App\Models\Product\Product')->where('commentable_id',$request->commentable_id)

         ->paginate($request->length, [(new Comment())->getTable() . '.*'], 'page', ($request->start / $request->length) + 1);

//     dump($comments);


        $totalLength = $comments->total();

       $starsCount =  $comments->groupBy('stars')->map->count();

        $stars = [
            'avg'=> round($comments->avg('stars'),2),
            'one'=>$starsCount[1]??0,
            'two'=>$starsCount[2]??0,
            'three'=>$starsCount[3]??0,
            'four'=>$starsCount[4]??0,
            'five'=>$starsCount[5]??0,
        ];

        $commentsArr = $comments->values()->toArray();

        foreach($commentsArr as &$comment){
            $comment['updated_at'] =   Carbon::parse($comment['updated_at'])->format('d/m/Y H:i');

        }

//        dump($commentsArr,$totalLength);
//        return 'ok';
        return response()->json(['comments'=>$commentsArr,'stars'=>$stars, 'totalLength' => $totalLength]);
    }
}
