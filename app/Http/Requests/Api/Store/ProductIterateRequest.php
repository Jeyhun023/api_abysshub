<?php

namespace App\Http\Requests\Api\Store;

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
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'source_code' => 'required',
            'description' => 'required',
            'price' => 'required|integer'
        ];
    }
}
