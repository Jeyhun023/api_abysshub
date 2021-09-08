<?php

namespace App\Http\Requests\Api\Forum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ThreadsVote;
use Illuminate\Validation\Rule;

class ThreadVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge([
            'thread_id' => $this->route('thread')->id,
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
            'type' => ['required', Rule::in(ThreadsVote::VOTE_TYPE_SELECT)],
            'thread_id' => ['required', Rule::unique('threads_vote')->where('thread_id', $this->thread_id)->where('user_id', $this->user_id)]
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
            'thread_id.unique' => trans('messages.have_voted'),
        ];
    }
}