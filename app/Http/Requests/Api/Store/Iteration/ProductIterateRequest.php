<?php

namespace App\Http\Requests\Api\Store\Iteration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ProfanityCheck;

class ProductIterateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'id' => $this->route('product')->id
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
            'id' => ['required', Rule::exists('products')],
            'name' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'description' => ['required', 'string', new ProfanityCheck()],
            'source_code' => 'required'
        ];
    }
}
