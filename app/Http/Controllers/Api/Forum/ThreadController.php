<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\ThreadsVote;
use App\Models\ThreadsComment;
use App\Http\Requests\Api\Forum\ThreadVoteRequest;
use App\Http\Requests\Api\Forum\ThreadUnvoteRequest;
use App\Http\Requests\Api\Forum\ThreadCommentRequest;
use App\Http\Requests\Api\Forum\ThreadRequest;
use Illuminate\Http\Request;
use App\Http\Resources\Forum\ThreadCollection;
use App\Http\Resources\Forum\ThreadCommentCollection;
use App\Http\Resources\Forum\ThreadCommentResource;
use App\Http\Resources\Forum\ThreadResource;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $threads = Thread::with(['category', 'user'])->orderBy('id', 'DESC')->limit(12)->get();
        return $this->successResponse(new ThreadCollection($threads));
    }
    
    public function show($id, $slug)
    {
        $thread = Thread::with(['category', 'user', 'userVotes'])
            ->where([
                'id' => $id,
                'slug' => $slug
            ])
            ->firstOrFail();
        $thread->increment('view_count');

        return $this->successResponse(new ThreadResource($thread));
    }

    public function store(ThreadRequest $request)
    {
        try {
            $thread = Thread::query()->create([
                'user_id' => auth()->user()->id, 
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                'tags' => $request->tags,
                'last_active_at' => now(),
            ]);

            return $this->successResponse(new ThreadResource($thread), trans('messages.thread_store_success'));
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

            return $this->successResponse($threadVote);
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

            return $this->successResponse(new ThreadCommentResource($threadComment));
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
