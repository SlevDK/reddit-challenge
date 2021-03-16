<?php

namespace App\Http\Resources\Board;

use Illuminate\Http\Resources\Json\JsonResource;

class BoardMemberResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->pivot->role,
        ];
    }
}
