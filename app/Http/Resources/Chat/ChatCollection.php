<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class ChatCollection extends ResourceCollection
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
                'opponent_user' => new UserResource( 
                    $message->user_id_from == auth()->id() ? $message->user_to : $message->user_from
                ),
                'messages' => [],
                'last_activity' => $message->last_activity->format('H:i'),
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
