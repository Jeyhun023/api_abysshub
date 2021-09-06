<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\ThreadCollection;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $threads = Thread::with(['answers', 'category', 'user'])->orderBy('id', 'DESC')->limit(12)->get();
        return $this->successResponse(new ThreadCollection($threads));
    }
}
