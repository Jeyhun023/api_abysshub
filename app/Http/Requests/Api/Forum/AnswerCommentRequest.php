<?php

namespace App\Http\Requests\Api\Forum;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;

class AnswerCommentRequest extends FormRequest
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
            'content' => ['required', new ProfanityCheck()]
        ];
    }
}
