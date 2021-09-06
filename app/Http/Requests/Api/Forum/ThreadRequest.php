<?php

namespace App\Http\Requests\Api\Forum;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;

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
            'category_id' => 'required|integer|exists:categories,id',
            'title' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'content' => ['required', new ProfanityCheck()],
            'tags' => 'required|max:255',
        ];
    }
}
