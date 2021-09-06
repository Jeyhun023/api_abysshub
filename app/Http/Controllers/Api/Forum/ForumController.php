<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Forum\ThreadRequest;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $threads = Thread::with(['category', 'user'])->orderBy('id', 'DESC')->limit(12)->get();
        return $this->successResponse(new ThreadCollection($threads));
    }
    
    public function show($id, $slug)
    {
        $thread = Thread::with(['answers.user', 'category', 'user'])
            ->where([
                'id' => $id,
                'slug' => $slug
            ])
            ->firstOrFail();
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

            return $this->successResponse(new ThreadResource($thread));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}
