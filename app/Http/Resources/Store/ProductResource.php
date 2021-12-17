<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Forum\ThreadCollection;

class ProductResource extends JsonResource
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
            // 'iterations' => $this->whenLoaded('iterations', new ProductCollection($this->iterations), null),
            'threads' => $this->whenLoaded('threads', new ThreadCollection($this->threads), null),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'rate' => $this->rate,
            'tags' => $this->tags,
            'download_count' => $this->download_count,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y')
        ];
    }
}
