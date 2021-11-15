<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class LinkedAnswerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($linked) {
            return [
                'id' => $linked->answer->id,
                'user' => new UserResource($linked->answer->user),
                'content' => $linked->answer->content,
                'upvote' => $linked->answer->upvote,
                'comment_count' => $linked->answer->comment_count,
                'user_votes' => $linked->answer->userVotes,
                'created_at' => $linked->answer->created_at->format('d/m/Y'),
                'updated_at' => $linked->answer->updated_at->format('d/m/Y'),
            ];
        })->toArray();
        return parent::toArray($request);
    }

}
