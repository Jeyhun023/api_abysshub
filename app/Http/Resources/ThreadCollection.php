<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
                'user' => new UserResource($thread->user),
                'category' => new CategoryResource($thread->category),
                'title' => $thread->title,
                'slug' => $thread->slug,
                'content' => $thread->content,
                'tags' => $thread->tags,
                'accepted_answer_id' => $thread->accepted_answer_id,
                'answer_count' => $thread->answer_count,
                'comment_count' => $thread->comment_count,
                'view_count' => $thread->view_count,
                'score' => $thread->score,
                'last_active_at' => $thread->last_active_at,
                'closed_at' => $thread->closed_at,
                'created_at' => $thread->created_at->format('d/m/Y'),
                'updated_at' => $thread->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
