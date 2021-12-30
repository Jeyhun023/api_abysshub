<?php

namespace App\Http\Requests\Api\Forum\Answer;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;
use Helper;

class AnswerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $linked_products = Helper::get_explode($this->linked_products);

        $this->merge([
            'id' => $this->route('answer')->id,
            'user_id' => auth()->user()->id,
            'linked_products' => $linked_products
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
            'content' => ['required', new ProfanityCheck()],
            'linked_products.*' => ['required', 'integer', 'exists:products,id']
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('messages.answer_error'),
        ];
    }
}
