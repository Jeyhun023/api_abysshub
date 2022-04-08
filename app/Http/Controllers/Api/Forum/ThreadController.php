<?php

namespace App\Http\Controllers\Api\Forum;

use App\Models\Thread;
use App\Models\Product;
use App\Models\Vote;
use App\Models\Comment;
use App\Models\LinkedProduct;
use App\Http\Requests\Api\Forum\Thread\ThreadVoteRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadUnvoteRequest;
use App\Http\Requests\Api\Forum\Comment\CommentRequest;
use App\Http\Requests\Api\Forum\Comment\CommentUpdateRequest;
use App\Http\Requests\Api\Forum\Comment\CommentDeleteRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadUpdateRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadDeleteRequest;
use App\Http\Resources\Forum\ThreadCollection;
use App\Http\Resources\Forum\ThreadResource;
use App\Http\Resources\Forum\CommentCollection;
use App\Http\Resources\Forum\CommentResource;
use Illuminate\Support\Str;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Events\ThreadElasticEvent;

class ThreadController extends Controller
{
    use ApiResponser;
    public $user;
    
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function index()
    {
        $threads = Thread::with(['user'])->orderBy('id', 'DESC')->limit(12)->get();
        return $this->successResponse(new ThreadCollection($threads));
    }
    
    public function show($id)
    {
        $thread = Thread::with(['user', 'userVotes', 'linked.product'])->findOrFail($id);
        $thread->increment('view_count');

        activity('thread')
            ->event('show')
            ->causedBy($this->user)
            ->performedOn($thread)
            ->withProperties(['query' => request()->query('query') ])
            ->log( request()->ip() );

        return $this->successResponse(new ThreadResource($thread));
    }

    public function store(ThreadRequest $request)
    {
        try {
            $thread = new Thread();
            $thread->user_id = auth()->user()->id;
            $thread->title = $request->title;
            $thread->description = $request->description;
            $thread->slug = Str::slug($request->title);
            $thread->content = $request->content;
            $thread->tags = collect( $request->tags );
            $thread->last_active_at = now();
            $thread->type = $request->type;
            if($request->type == 3){
                $thread->product_id = $request->product_id;
            }
            $thread->save();

            foreach($request->linked_products as $product){
                LinkedProduct::create([
                    'linkable_id' => $thread->id,
                    'linkable_type' => Thread::class, 
                    'product_id' => $product
                ]);
            }

            $thread = new ThreadResource($thread);
            event(new ThreadElasticEvent($thread));
            
            return $this->successResponse($thread, trans('messages.thread_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
    
    public function update(Thread $thread, ThreadUpdateRequest $request)
    {
        try {
            $thread->title = $request->title;
            $thread->slug = Str::slug($request->title);
            $thread->content = $request->content;
            $thread->tags = collect( explode(',' , $request->tags) );
            $thread->save();
            $thread = new ThreadResource($thread);

            $thread_linked_products = LinkedProduct::where([
                'linkable_id' => $thread->id,
                'linkable_type' => Thread::class
            ])->pluck('product_id')->toArray();
           
            $need_delete = array_diff($thread_linked_products, $request->linked_products);
            $need_add = array_diff($request->linked_products , $thread_linked_products);

            LinkedProduct::where([
                'linkable_id' => $thread->id,
                'linkable_type' => Thread::class
            ])
            ->WhereIn('product_id', $need_delete)
            ->delete();

            foreach($need_add as $product){
                LinkedProduct::create([
                    'linkable_id' => $thread->id,
                    'linkable_type' => Thread::class, 
                    'product_id' => $product
                ]);
            }
           
            event(new ThreadElasticEvent($thread));

            return $this->successResponse($thread, trans('messages.thread_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function delete(Thread $thread, ThreadDeleteRequest $request)
    {
        try {
            $thread->delete();

            return $this->successResponse($thread, trans('messages.thread_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function vote(Thread $thread, ThreadVoteRequest $request)
    {
        try {
            $threadVote = Vote::where([
                'voteable_id' => $thread->id,
                'voteable_type' => Thread::class, 
                'user_id' => auth()->user()->id, 
            ])->where('type', '!=', $request->type)->first();

            if(!empty($threadVote)){
                $thread->decrement($threadVote->type);
                $threadVote->delete();
            }

            $threadVote = Vote::firstOrCreate([
                'voteable_id' => $thread->id,
                'voteable_type' => Thread::class, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ]);

            $thread->increment($request->type);

            return $this->successResponse($threadVote, trans('messages.vote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function unvote(Thread $thread, ThreadUnvoteRequest $request)
    {
        try {
            $threadVote = Vote::query()->where([
                'voteable_id' => $thread->id,
                'voteable_type' => Thread::class, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ])->first();
            $threadVote->delete();
            $thread->decrement($request->type);

            return $this->successResponse($threadVote, trans('messages.unvote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function comment(Thread $thread, CommentRequest $request)
    {
        try {
            $threadComment = Comment::query()->create([
                'commentable_id' => $thread->id,
                'commentable_type' => Thread::class, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);
            $thread->increment('comment_count');

            return $this->successResponse(new CommentResource($threadComment), trans('messages.comment_success'));
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
            
            return $this->successResponse($comment, trans('messages.comment_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function getComment($thread)
    {
        $threadComments = Comment::where([
            'commentable_id' => $thread,
            'commentable_type' => Thread::class,
        ])->with('user')->get();
        return $this->successResponse(new CommentCollection($threadComments));
    }

    public function search()
    {
        $threads = Thread::with('user')->withCount('linkedProducts')
            ->orderByDesc('id')->paginate(10);
        return $this->successResponse(new ThreadCollection($threads), null);
    }
}
