<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\ApiRequest;

class CommentListRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'limit' => 'numeric|max:100',
            'offset' => 'numeric|min:0',
            'order' => 'string|in:ASC,DESC',
        ];
    }
}
