<?php

namespace App\Http\Requests\Thread;

use Illuminate\Foundation\Http\FormRequest;

class ThreadTagRequest extends FormRequest
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
            'tag' => 'required|string|max:255',
        ];
    }
}
