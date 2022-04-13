<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;

class ProductIterationResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'note' => $this->note,
            'rate' => $this->rate,
            'download_count' => $this->download_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
