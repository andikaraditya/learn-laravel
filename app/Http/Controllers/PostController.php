<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return PostDetailResource::collection($posts->loadMissing(["user:id,firstname,lastname", "comments"]));
    }

    public function show($id)
    {
        $post = Post::with(["user:id,firstname,lastname", "comments:id,post_id,user_id,comments_content"])->findOrFail($id);

        return new PostDetailResource($post);
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|max:255",
            "news_content" => "required",
        ]);

        if ($request->file) {
            $fileName = $this->generateRandomString(30);
            $extension = $request->file->extension();

            Storage::putFileAs("images", $request->file, $fileName.".".$extension);
            $request["image"] = $fileName.".".$extension;
        }
        
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

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
