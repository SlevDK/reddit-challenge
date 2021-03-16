<?php

namespace App\Http\Resources\Thread;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TagResourceCollection extends ResourceCollection
{
    public $collects = TagResource::class;

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
