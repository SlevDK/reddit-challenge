<?php

namespace App\Http\Requests\Board;

use App\Http\Requests\ApiRequest;
use App\Models\Board;
use Illuminate\Validation\Rule;

class SetRoleRequest extends ApiRequest
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
            'role' => ['required', 'string', Rule::in([Board::ROLE_MEMBER, Board::ROLE_MODERATOR, Board::ROLE_BANNED])]
        ];
    }
}
