<?php

namespace App\Http\Requests\Api\Forum\Answer;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;

class AnswerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'id' => $this->route('answer')->id,
            'user_id' => auth()->user()->id
        ]);
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
            'id' => ['required', Rule::exists('answers')->where('user_id', $this->user_id)],
            'content' => ['required', new ProfanityCheck()]
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('messages.answer_error'),
        ];
    }
}
