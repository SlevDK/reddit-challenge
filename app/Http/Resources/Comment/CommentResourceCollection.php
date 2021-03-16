<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentResourceCollection extends ResourceCollection
{
    public $collects = CommentResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
