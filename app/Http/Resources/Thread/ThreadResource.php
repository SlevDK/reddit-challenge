<?php

namespace App\Http\Resources\Thread;

use App\Http\Resources\ProfileResource;
use App\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
            'tags' => new TagResourceCollection($this->tags),
        ];
    }
}
