<?php

namespace App\Http\Requests\Api\Forum\Answer;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Helper;

class AnswerRequest extends FormRequest
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
            'content' => ['required', new ProfanityCheck()],
            'linked_products.*' => ['required', 'integer', 'exists:products,id']
        ];
    }
}
