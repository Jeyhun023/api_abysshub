<?php

namespace App\Http\Requests\Api\Forum\Thread;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;
use Helper;

class ThreadUpdateRequest extends FormRequest
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
            'id' => $this->route('thread')->id,
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
            'id' => ['required', Rule::exists('threads')->where('user_id', $this->user_id)],
            'title' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'content' => ['required', new ProfanityCheck()],
            'tags' => 'required|max:255',
            'linked_products.*' => ['required', 'integer', 'exists:products,id']
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('messages.question_error'),
        ];
    }
}
