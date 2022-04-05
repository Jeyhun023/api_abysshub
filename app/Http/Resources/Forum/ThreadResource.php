<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;
use App\Models\Thread;

class ThreadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'linked_products' => $this->whenLoaded('linked'),
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'description' => $this->description,
            'tags' => $this->tags,
            'accepted_answer_id' => $this->accepted_answer_id,
            'answer_count' => $this->answer_count,
            'comment_count' => $this->comment_count,
            'view_count' => $this->view_count,
            'upvote' => $this->upvote,
            'downvote' => $this->downvote,
            'user_vote' => $this->userVotes?->type, 
            'type' => $this->type,
            'last_active_at' => $this->last_active_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y')
        ];
    }
}
