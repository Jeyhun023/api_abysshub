<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'commentable_id' => $this->commentable_id,
            'content' => $this->content,
            'isEdited' => ($this->created_at == $this->updated_at) ? false : true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
