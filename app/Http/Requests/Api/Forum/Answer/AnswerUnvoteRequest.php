<?php

namespace App\Http\Requests\Api\Forum\Answer;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AnswersVote;
use Illuminate\Validation\Rule;

class AnswerUnvoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'answer_id' => $this->route('answer')->id,
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
            'type' => ['required', Rule::in(AnswersVote::VOTE_TYPE_SELECT)],
            'answer_id' => ['required', Rule::exists('answers_vote')
                    ->where('answer_id', $this->answer_id)
                    ->where('user_id', $this->user_id)
                    ->where('type', $this->type)
                ]
        ];
    }

    public function attributes()
    {
        return [
            'type'  => 'vote type',
        ];
    }

    public function messages()
    {
        return [
            'answer_id.exists' => trans('messages.havent_voted'),
        ];
    }
}
