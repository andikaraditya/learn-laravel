<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "image" => $this->image,
            "author" => $this->author,
            "news_content" => $this->news_content,
            "created_at" => date("d-m-Y", strtotime($this->created_at)),
            "user" => $this->whenLoaded("user"),
            "comments" => $this->whenLoaded("comments", function(){
                return collect($this->comments)->each(function($comment){
                    $comment->commentator;
                    return $comment;
                });
            }),
            "comments_total" => $this->whenLoaded("comments", function(){
                return count($this->comments);
            }) 
            ];
    }
}
