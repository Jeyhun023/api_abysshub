<?php

namespace App\Http\Requests\Api\Forum\Thread;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Illuminate\Validation\Rule;
use Helper;

class ThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    { 
        $description = Helper::get_description($this['content']);
        $tags = Helper::get_explode($this->tags);
        $linked_products = Helper::get_explode($this->linked_products);

        $this->merge([
            'description' => $description,
            'tags' => $tags,
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
            'title' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'description' => ['required', 'string', 'min:50', new ProfanityCheck()],
            'content' => ['required'],
            'type' => ['required', Rule::in(['1', '2', '3'])],
            'tags' => ['required', 'array', 'max:10', 'min:5'],
            'product_id' => ['required_if:type,==,3', 'exists:products,id'],
            'linked_products.*' => ['required', 'integer', 'exists:products,id'],
        ];
    }
}
