<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Chat\MessageCollection;

class ChatResource extends JsonResource
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
            'opponent_user' => new UserResource( 
                $this->user_id_from == auth()->id() ? $this->user_to : $this->user_from
            ),
            'messages' => $this->whenLoaded('messages', new MessageCollection($this->messages), null),
            'last_activity' => $this->last_activity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
