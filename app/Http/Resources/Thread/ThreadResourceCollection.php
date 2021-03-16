<?php

namespace App\Http\Resources\Thread;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ThreadResourceCollection extends ResourceCollection
{
    public $collects = ThreadResource::class;

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
