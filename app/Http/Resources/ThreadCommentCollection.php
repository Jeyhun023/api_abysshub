<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ThreadCommentCollection extends ResourceCollection
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
                'thread_id' => $comment->thread_id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->format('d/m/Y'),
                'updated_at' => $comment->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
