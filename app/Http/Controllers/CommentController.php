<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //

    public function store(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            "post_id" => "required|exists:posts,id",
            "comments_content" => "required"
        ]);

        $request["user_id"] = $user->id;

        $comment = Comment::create($request->all());

        return new CommentResource($comment->loadMissing(["commentator:id,firstname,lastname"]));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            "comments_content" => "required"
        ]);
        $comment = Comment::findOrFail($id);
        $comment->update($request->all());
        return new CommentResource($comment->loadMissing(["commentator:id,firstname,lastname"]));
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return new CommentResource($comment->loadMissing(["commentator:id,firstname,lastname"]));
    }
}
