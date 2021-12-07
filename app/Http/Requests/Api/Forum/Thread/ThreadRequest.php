<?php

namespace App\Http\Requests\Api\Forum\Thread;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;

class ThreadRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'content' => ['required', new ProfanityCheck()],
            'type' => ['required', Rule::in(['1', '2', '3'])],
            'tags' => 'required|max:255',
            'product_id' => ['required_if:type,==,3', 'exists:products,id'],
        ];
    }
}
