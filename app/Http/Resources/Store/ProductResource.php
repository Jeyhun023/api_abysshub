<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Forum\ThreadCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $response =[
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'iterations' => new ProductCollection($this->whenLoaded('iterations')),
            'threads' => new ThreadCollection($this->whenLoaded('threads')),
            'name' => $this->name,
            'slug' => $this->slug,
            'extension' => $this->extension,
            'details' => $this->description,
            'price' => $this->price,
            'rate' => $this->rate,
            'tags' => $this->tags,
            'isFree' => $this->is_free,
            'isPublic' => $this->is_public,
            'isSubmitted' => $this->is_submitted,
            'download_count' => $this->download_count,
            'userCave' => $this->userCave == null ? false : true,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y')
        ];
        
        $response["draft"] = $this->draft;
        $response["sourceCode"] = $this->file;

        return $response;
    }
}
