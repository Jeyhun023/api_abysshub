<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;

class RatingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'value' => $this->value,
            'content' => $this->content,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y')
        ];
    }
}
