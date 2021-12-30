<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;
use App\Models\Thread;

class ThreadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($thread) {
            return [
                'id' => $thread->id,
                'user' => new UserResource($thread->user),
                'title' => $thread->title,
                'slug' => $thread->slug,
                'description' => $thread->description,
                'tags' => $thread->tags,
                'accepted_answer_id' => $thread->accepted_answer_id,
                'answer_count' => $thread->answer_count,
                'comment_count' => $thread->comment_count,
                'view_count' => $thread->view_count,
                'upvote' => $thread->upvote,
                'downvote' => $thread->downvote,
                'type' => $thread->type,
                'last_active_at' => $thread->last_active_at,
                'closed_at' => $thread->closed_at,
                'created_at' => $thread->created_at->format('d/m/Y'),
                'updated_at' => $thread->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
