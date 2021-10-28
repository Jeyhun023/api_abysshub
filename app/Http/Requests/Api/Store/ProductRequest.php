<?php

namespace App\Http\Requests\Api\Store;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use App\Rules\PlagiarismCheck;

class ProductRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:255', new ProfanityCheck()],
            'source_code' => ['required', new PlagiarismCheck()],
            'description' => 'required',
            'price' => 'required|integer'
        ];
    }
}
