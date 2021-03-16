<?php

namespace App\Http\Resources\Thread;

use App\Http\Resources\Comment\CommentResourceCollection;
use App\Http\Resources\ProfileResource;
use App\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadCommentsResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => ($this->status == Thread::THREAD_STATUS_OPENED) ? 'opened' : 'closed',
            'author' => new ProfileResource($this->author),
            'comments' => new CommentResourceCollection($this->comments),
            'tags' => new TagResourceCollection($this->tags),
        ];
    }
}
