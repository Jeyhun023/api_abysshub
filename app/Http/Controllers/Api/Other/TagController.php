<?php

namespace App\Http\Controllers\Api\Other;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use ApiResponser;

    public function search($query)
    {
        try {
            $tags = Tag::where('name', 'LIKE', '%' . $query . '%')->get();
            
            return $this->successResponse($tags, null);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}
