<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Thread;
use App\Models\AnswersVote;
use App\Models\AnswersComment;
use App\Models\AnswerLinkedProduct;
use App\Http\Resources\Forum\AnswerResource;
use App\Http\Resources\Forum\AnswerCollection;
use App\Http\Resources\Forum\LinkedProductCollection;
use App\Http\Resources\Forum\AnswerCommentCollection;
use App\Http\Resources\Forum\AnswerCommentResource;
use App\Http\Requests\Api\Forum\AnswerRequest;
use App\Http\Requests\Api\Forum\AnswerUpdateRequest;
use App\Http\Requests\Api\Forum\AnswerDeleteRequest;
use App\Http\Requests\Api\Forum\AnswerVoteRequest;
use App\Http\Requests\Api\Forum\AnswerUnvoteRequest;
use App\Http\Requests\Api\Forum\AnswerCommentRequest;
use App\Http\Requests\Api\Forum\AnswerCommentUpdateRequest;
use App\Http\Requests\Api\Forum\AnswerCommentDeleteRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use DB;

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

            foreach(json_decode($request->linked_products) as $product){
                AnswerLinkedProduct::create([
                    'answer_id' => $answer->id,
                    'product_id' => $product
                ]);
            }

            return $this->successResponse(new AnswerResource($answer), trans('messages.answer_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
 
    public function update(Answer $answer, AnswerUpdateRequest $request)
    {
        try {
            $answer->content = $request->content;
            $answer->save();

            return $this->successResponse(new AnswerResource($answer), trans('messages.answer_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function delete(Answer $answer, AnswerDeleteRequest $request)
    {
        try {
            $answer->thread->decrement('answer_count');
            $answer->delete();

            return $this->successResponse(null, trans('messages.answer_delete_success'));
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

            return $this->successResponse($answerVote, trans('messages.vote_success'));
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

            return $this->successResponse(new AnswerCommentResource($answerComment), trans('messages.comment_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentUpdate(AnswersComment $comment, AnswerCommentUpdateRequest $request)
    {
        try {
            $comment->content = $request->content;
            $comment->save();
            
            return $this->successResponse(new AnswerCommentResource($comment), trans('messages.comment_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentDelete(AnswersComment $comment, AnswerCommentDeleteRequest $request)
    {
        try {
            $comment->answer->decrement('comment_count');
            $comment->delete();
            
            return $this->successResponse(null, trans('messages.comment_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function getComment($answer)
    {
        $answerComments = AnswersComment::where('answer_id', $answer)->with('user')->get();
        return $this->successResponse(new AnswerCommentCollection($answerComments));
    }
    
    public function loadAnswers($thread)
    {
        $loadAnswers = Answer::with('linked.product')->where('thread_id', $thread)->paginate(5);

        return new AnswerCollection($loadAnswers);
    }

    public function loadProducts($thread)
    {
        $loadProducts = AnswerLinkedProduct::with('product')->whereHas('answer.thread', function ($q) use ($thread){
                $q->where('id', $thread);   
            })
            ->select('product_id', DB::raw('count(*) as total'))
            ->groupBy('product_id')
            ->paginate(5);
        
        return new LinkedProductCollection($loadProducts);
    }
    
    public function getAnswers($thread, $product)
    {
        $getAnswers = Answer::with('linked.product')
            ->whereHas('thread', function ($q) use ($thread){
                $q->where('id', $thread);   
            },'linked', function ($q) use ($product){
                $q->where('product_id', $product);   
            }
        )->paginate(5);
        
        return new AnswerCollection($getAnswers);
    }

}
