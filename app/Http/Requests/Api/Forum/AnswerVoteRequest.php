<?php

namespace App\Http\Requests\Api\Forum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AnswersVote;
use Illuminate\Validation\Rule;

class AnswerVoteRequest extends FormRequest
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
            'type' => ['required', Rule::in(AnswersVote::VOTE_TYPE_SELECT)]
        ];
    }
}
