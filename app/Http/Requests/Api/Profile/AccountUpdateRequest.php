<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProfanityCheck;
use Helper;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $skills = Helper::get_explode($this->skills);

        $this->merge([
            'skills' => $skills
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
            'name' => ['required', 'string', 'max:70', new ProfanityCheck()],
            'fullname' => ['nullable', 'string', 'max:70', new ProfanityCheck()],
            'image' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000', new ProfanityCheck()],
            'skills.*' => ['nullable', 'string', 'max:1000', 'exists:skills,name'],
        ];
    }
}
