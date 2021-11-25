<?php

namespace App\Http\Requests\Api\Store\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'id' => $this->route('product')->id,
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
            'id' => ['required', Rule::exists('products')->where('user_id', $this->user_id)]
        ];
    }
    
    public function messages()
    {
        return [
            'id.exists' => trans('messages.product_error'),
        ];
    }
}
