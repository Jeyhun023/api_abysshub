<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class AnswerCommentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($comment) {
            return [
                'id' => $comment->id,
                'user' => new UserResource($comment->user),
                'answer_id' => $comment->answer_id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->format('d/m/Y'),
                'updated_at' => $comment->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
