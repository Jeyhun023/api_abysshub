<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;

class AnswerResource extends JsonResource
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
            'content' => $this->content,
            'upvote' => $this->upvote,
            'downvote' => $this->downvote,
            'comment_count' => $this->comment_count == null ? 0 : $this->comment_count,
            'user_vote' => $this->userVotes?->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
