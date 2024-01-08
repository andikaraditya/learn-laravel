<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return PostResource::collection($posts->loadMissing(["user:id,firstname,lastname", "comments"]));
    }

    public function show($id)
    {
        $post = Post::with(["user:id,firstname,lastname", "comments:id,post_id,user_id,comments_content"]) -> findOrFail($id);

        return new PostDetailResource($post);
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|max:255",
            "news_content" => "required",
        ]);

        $request["author"] = Auth::user()->id;

        $post = Post::create($request->all());

        return new PostDetailResource($post->loadMissing("user:id,firstname,lastname"));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "title" => "required|max:255",
            "news_content" => "required",
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing("user:id,firstname,lastname"));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing("user:id,firstname,lastname"));
    }
}
