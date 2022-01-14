<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class CommentCollection extends ResourceCollection
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
                'commentable_id' => $comment->commentable_id,
                'content' => $comment->content,
                'isEdited' => ($this->created_at == $this->updated_at) ? false : true,
                'created_at' => $comment->created_at->format('d/m/Y'),
                'updated_at' => $comment->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
