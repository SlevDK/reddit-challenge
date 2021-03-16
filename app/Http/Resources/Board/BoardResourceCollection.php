<?php

namespace App\Http\Resources\Board;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BoardResourceCollection extends ResourceCollection
{
    public $collects = BoardResource::class;

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
