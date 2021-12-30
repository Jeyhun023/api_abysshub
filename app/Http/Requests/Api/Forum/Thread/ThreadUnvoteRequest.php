<?php

namespace App\Http\Requests\Api\Forum\Thread;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vote;
use Illuminate\Validation\Rule;

class ThreadUnvoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'voteable_id' => $this->route('thread')->id,
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
            'type' => ['required', Rule::in(Vote::VOTE_TYPE_SELECT)],
            'voteable_id' => ['required', Rule::exists('votes')
                    ->where('voteable_type', 'App\Models\Thread')
                    ->where('voteable_id', $this->voteable_id)
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
            'voteable_id.exists' => trans('messages.havent_voted'),
        ];
    }
}
