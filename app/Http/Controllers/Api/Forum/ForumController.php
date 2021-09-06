<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $threads = Thread::with(['category', 'user'])->orderBy('id', 'DESC')->limit(12)->get();
        return $this->successResponse(new ThreadCollection($threads));
    }
    
    public function show($slug)
    {
        $thread = Thread::with(['answers.user', 'category', 'user'])->where('slug', $slug)->firstOrFail();
        return $this->successResponse(new ThreadResource($thread));
    }
}
