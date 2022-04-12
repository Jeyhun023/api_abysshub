<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;

class RegisterRequest extends FormRequest
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
            'fullname' => ['sometimes', 'string', 'max:50', new ProfanityCheck()],
            'username' => ['required', 'string', 'max:15', new ProfanityCheck(), 'unique:users,username'],
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', 'string', 'min:7'] // 'regex:/^[a-zA-Z0-9]*([a-zA-Z][0-9]|[0-9][a-zA-Z])[a-zA-Z0-9]*$/'
        ];
    }
}
