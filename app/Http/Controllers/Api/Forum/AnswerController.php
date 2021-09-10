<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Thread;
use App\Models\AnswersVote;
use App\Models\AnswersComment;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\AnswerCommentCollection;
use App\Http\Resources\AnswerCommentResource;
use App\Http\Requests\Api\Forum\AnswerRequest;
use App\Http\Requests\Api\Forum\AnswerVoteRequest;
use App\Http\Requests\Api\Forum\AnswerUnvoteRequest;
use App\Http\Requests\Api\Forum\AnswerCommentRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AnswerController extends Controller
{
    use ApiResponser;

    public function store(Thread $thread, AnswerRequest $request)
    {
        try {
            $answer = Answer::query()->create([
                'thread_id' => $thread->id, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);
            $thread->increment('answer_count');

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

            return $this->successResponse($answerVote, trans('messages.unvote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function comment(Answer $answer, AnswerCommentRequest $request)
    {
        try {
            $answerComment = AnswersComment::query()->create([
                'answer_id' => $answer->id, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);
            $answer->increment('comment_count');

            return $this->successResponse(new AnswerCommentResource($answerComment));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
    
    public function getComment($answer)
    {
        $answerComments = AnswersComment::where('answer_id', $answer)->with('user')->orderBy('id','DESC')->get();
        return $this->successResponse(new AnswerCommentCollection($answerComments));
    }
}
