<?php

namespace App\Http\Requests\Api\Store\Product;

use Illuminate\Support\Str;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->name){
            $this->merge(['slug' => Str::slug($this->name)]);
        }
        if($this->tags){
            $this->merge(['tags' => explode(',' , $this->tags)]);
        }
        if($this->details){
            $this->merge(['description' => json_encode($this->details)]);
        }
        $this->merge([
            'id' => $this->route('product')->id,
            'user_id' => auth()->user()->id,
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
            'name' => ['sometimes' , 'max:255', new ProfanityCheck()],
            'slug' => 'sometimes',
            'price' => 'sometimes|integer|min:1|max:3',
            'draft' => 'sometimes',
            'description' => 'sometimes',
            'details.description' => 'sometimes|min:50|max:5000|string',
            'details.applicability' => 'sometimes|min:50|max:5000|string',
            'details.problemFormulation' => 'sometimes|min:50|max:5000|string',
            'details.*' => 'sometimes|min:50|max:5000|string',
            'tags' => 'sometimes|array|min:3|max:5',
            'is_public' => 'sometimes|nullable|boolean',
            'is_free' => 'sometimes|nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => trans('messages.product_error'),
        ];
    }
}
