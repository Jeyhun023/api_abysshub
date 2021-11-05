<?php

namespace App\Http\Requests\Api\Store;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
            'id' => ['required', Rule::exists('products')->where('user_id', $this->user_id)],
            'category_id' => [Rule::requiredIf($this->route('product')->status == 1), 'exists:categories,id'],
            'name' => [Rule::requiredIf($this->route('product')->status == 1) , 'max:255', new ProfanityCheck()],
            'description' => Rule::requiredIf($this->route('product')->status == 1),
            'price' => [Rule::requiredIf($this->route('product')->status == 1), 'max:1000']
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('messages.product_error'),
        ];
    }
}
