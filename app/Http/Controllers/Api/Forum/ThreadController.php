<?php

namespace App\Http\Controllers\Api\Forum;

use App\Models\Thread;
use App\Models\Product;
use App\Models\ThreadsVote;
use App\Models\ThreadsComment;
use App\Models\ThreadLinkedProduct;
use App\Http\Requests\Api\Forum\Thread\ThreadVoteRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadUnvoteRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadCommentRequest;
use App\Http\Requests\Api\Forum\Thread\ProductThreadRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadCommentUpdateRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadCommentDeleteRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadUpdateRequest;
use App\Http\Requests\Api\Forum\Thread\ThreadDeleteRequest;
use App\Http\Resources\Forum\ThreadCollection;
use App\Http\Resources\Forum\ThreadCommentCollection;
use App\Http\Resources\Forum\ThreadCommentResource;
use App\Http\Resources\Forum\ThreadResource;
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
    
    public function show($id, $slug)
    {
        $thread = Thread::with(['user', 'userVotes', 'linked.product'])
            ->where([
                'id' => $id,
                'slug' => $slug
            ])
            ->firstOrFail();
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
        return $request->description;
        try {
            $thread = new Thread();
            $thread->user_id = auth()->user()->id;
            $thread->title = $request->title;
            $thread->description = $request->description;
            $thread->slug = Str::slug($request->title);
            $thread->content = $request->content;
            $thread->tags = collect( explode(',' , $request->tags) );
            $thread->last_active_at = now();
            $thread->type = $request->type;
            if($request->type == 3){
                $thread->product_id = $request->product_id;
            }
            $thread->save();

            if ($request->has('linked_products')) {
                foreach(json_decode($request->linked_products) as $product){
                    ThreadLinkedProduct::create([
                        'thread_id' => $thread->id,
                        'product_id' => $product
                    ]);
                }
            }

            $thread = new ThreadResource($thread);
            event(new ThreadElasticEvent($thread));
            
            return $this->successResponse($thread, trans('messages.thread_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
    
    public function productDiscuss(Product $product, ProductThreadRequest $request)
    {
        try {
            $thread = Thread::query()->create([
                'product_id' => $product->id, 
                'user_id' => auth()->user()->id, 
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                'tags' => $request->tags,
                'last_active_at' => now(),
            ]);
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

            return $this->successResponse(null, trans('messages.thread_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function vote(Thread $thread, ThreadVoteRequest $request)
    {
        try {
            $threadVote = ThreadsVote::query()->create([
                'thread_id' => $thread->id, 
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
            $threadVote = ThreadsVote::query()->where([
                'thread_id' => $thread->id, 
                'user_id' => auth()->user()->id, 
                'type' => $request->type
            ])->delete();
            $thread->decrement($request->type);

            return $this->successResponse(null, trans('messages.unvote_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function comment(Thread $thread, ThreadCommentRequest $request)
    {
        try {
            $threadComment = ThreadsComment::query()->create([
                'thread_id' => $thread->id, 
                'user_id' => auth()->user()->id, 
                'content' => $request->content
            ]);
            $thread->increment('comment_count');

            return $this->successResponse(new ThreadCommentResource($threadComment), trans('messages.comment_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentUpdate(ThreadsComment $comment, ThreadCommentUpdateRequest $request)
    {
        try {
            $comment->content = $request->content;
            $comment->save();
            
            return $this->successResponse(new ThreadCommentResource($comment), trans('messages.comment_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function commentDelete(ThreadsComment $comment, ThreadCommentDeleteRequest $request)
    {
        try {
            $comment->thread->decrement('comment_count');
            $comment->delete();
            
            return $this->successResponse(null, trans('messages.comment_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function getComment($thread)
    {
        $threadComments = ThreadsComment::where('thread_id', $thread)->with('user')->get();
        return $this->successResponse(new ThreadCommentCollection($threadComments));
    }

}
