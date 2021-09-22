<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class MessageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($message) {
            return [
                'id' => $message->id,
                'user' => new UserResource($message->user),
                'content' => $message->content,
                'created_at' => $message->created_at->format('d/m/Y'),
                'updated_at' => $message->updated_at->format('d/m/Y'),
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
