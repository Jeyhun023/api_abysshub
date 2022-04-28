<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Thread;
use App\Models\Vote;
use App\Models\Comment;
use App\Models\LinkedProduct;
use App\Http\Resources\Forum\AnswerResource;
use App\Http\Resources\Forum\AnswerCollection;
use App\Http\Resources\Forum\LinkedProductCollection;
use App\Http\Resources\Forum\CommentCollection;
use App\Http\Resources\Forum\CommentResource;
use App\Http\Requests\Api\Forum\Answer\AnswerRequest;
use App\Http\Requests\Api\Forum\Answer\AnswerUpdateRequest;
use App\Http\Requests\Api\Forum\Answer\AnswerDeleteRequest;
use App\Http\Requests\Api\Forum\Answer\AnswerVoteRequest;
use App\Http\Requests\Api\Forum\Answer\AnswerUnvoteRequest;
use App\Http\Requests\Api\Forum\Comment\CommentRequest;
use App\Http\Requests\Api\Forum\Comment\CommentUpdateRequest;
use App\Http\Requests\Api\Forum\Comment\CommentDeleteRequest;
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
            
            foreach($request->linked_products as $product){
                LinkedProduct::create([
                    'linkable_id' => $answer->id,
                    'linkable_type' => Answer::class, 
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

            $answer_linked_products = LinkedProduct::where([
                'linkable_id' => $answer->id,
                'linkable_type' => Answer::class
            ])->pluck('product_id')->toArray();
           
            $need_delete = array_diff($answer_linked_products, $request->linked_products);
            $need_add = array_diff($request->linked_products , $answer_linked_products);

            LinkedProduct::where([
                'linkable_id' => $answer->id,
                'linkable_type' => Answer::class
            ])
            ->WhereIn('product_id', $need_delete)
            ->delete();

            foreach($need_add as $product){
                LinkedProduct::create([
                    'linkable_id' => $answer->id,
                    'linkable_type' => Answer::class, 
                    'product_id' => $product
                ]);
            }

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

            return $this->successResponse($answer, trans('messages.answer_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function vote(Answer $answer, AnswerVoteRequest $request)
    {
        try {
            $answerVote = Vote::where([
                'voteable_id' => $answer->id,
                'voteable_type' => Answer::class, 
                'user_id' => auth()->user()->id, 
            ])->where('type', '!=', $request->type)->first();

            if(!empty($answerVote)){
                $answer->decrement($answerVote->type);
                $answerVote->delete();
            }

            $answerVote = Vote::firstOrCreate([
                'voteable_id' => $answer->id,
                'voteable_type' => Answer::class, 
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
            $answerVote = Vote::query()->where([
                'voteable_id' => $answer->id,
                'voteable_type' => Answer::class, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ])->first();
            $answerVote->delete();
            $answer->decrement($request->type);

            return $this->successResponse($answerVote, trans('messages.unvote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function comment(Answer $answer, CommentRequest $request)
    {
        try {
            $answerComment = Comment::query()->create([
                'commentable_id' => $answer->id,
                'commentable_type' => Answer::class, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);
            $answer->increment('comment_count');

            return $this->successResponse(new CommentResource($answerComment), trans('messages.comment_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentUpdate(Comment $comment, CommentUpdateRequest $request)
    {
        try {
            $comment->content = $request->content;
            $comment->save();
            
            return $this->successResponse(new CommentResource($comment), trans('messages.comment_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentDelete(Comment $comment, CommentDeleteRequest $request)
    {
        try {
            $comment->commentable->decrement('comment_count');
            $comment->delete();
            
            return $this->successResponse(null, trans('messages.comment_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function getComment($answer)
    {
        $answerComments = Comment::where([
            'commentable_id' => $answer,
            'commentable_type' => Answer::class,
        ])->with('user')->get();
        return $this->successResponse(new CommentCollection($answerComments));
    }
    
    public function loadAnswers($thread)
    {
        $loadAnswers = Answer::with('linked.product')
            ->where('thread_id', $thread)
            ->orderBy('upvote');
        if(auth('api')->check()){
            $loadAnswers = $loadAnswers->orderByRaw("CASE WHEN user_id = ".auth('api')->id()." THEN 1 ELSE 0 END DESC");
        }
        $loadAnswers = $loadAnswers->paginate(5);
        
        return $this->successResponse(new AnswerCollection($loadAnswers));
    }

    public function loadProducts($thread)
    {
        $loadProducts = LinkedProduct::where([
                'linkable_type' => Answer::class
            ])->whereHas('product', function ($query){
                $query->submitted();   
                $query->where('is_public', 1);   
            })->whereHas('answer.thread', function ($q) use ($thread){
                $q->where('id', $thread);   
            })
            ->select('product_id', DB::raw('count(*) as total'))
            ->groupBy('product_id')
            ->paginate(5);
        
        return $this->successResponse(new LinkedProductCollection($loadProducts));
    }
    
    public function getAnswers($thread, $product)
    {
        $getAnswers = Answer::with('linked.product')
            ->whereHas('thread', function ($q) use ($thread){
                $q->where('id', $thread);   
            })
            ->whereHas('linked', function ($q) use ($product){
                $q->where('product_id', $product);   
            })
            ->paginate(5);
        
        return $this->successResponse(new AnswerCollection($getAnswers));
    }

}
