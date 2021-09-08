<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\AnswersVote;
use App\Http\Resources\AnswerResource;
use App\Http\Requests\Api\Forum\AnswerRequest;
use App\Http\Requests\Api\Forum\AnswerVoteRequest;
use App\Http\Requests\Api\Forum\AnswerUnvoteRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AnswerController extends Controller
{
    use ApiResponser;

    public function store($id, AnswerRequest $request)
    {
        try {
            $answer = Answer::query()->create([
                'thread_id' => $id, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);

            return $this->successResponse(new AnswerResource($answer), trans('messages.answer_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
 
    public function vote(Answer $answer, AnswerVoteRequest $request)
    {
        try {
            $answerVote = AnswersVote::query()->create([
                'answer_id' => $answer->id, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ]);
            $answer->increment($request->type);

            return $this->successResponse($answerVote);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    
    public function unvote(Answer $answer, AnswerUnvoteRequest $request)
    {
        try {
            $answerVote = AnswersVote::query()->where([
                'answer_id' => $answer->id, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ])->delete();
            $answer->decrement($request->type);

            return $this->successResponse(null, trans('messages.unvote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}
