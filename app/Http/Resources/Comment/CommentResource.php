<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\ProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'text' => $this->text,
            'author' => new ProfileResource($this->author),
        ];
    }
}
