<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Forum\ThreadCollection;
use App\Http\Resources\Other\ImageCollection;
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
            'images' => new ImageCollection($this->whenLoaded('images')),
            'name' => $this->name,
            'slug' => $this->slug,
            'extension' => $this->extension,
            'details' => $this->description,
            'price' => $this->price,
            'rate' => $this->rate,
            'tags' => $this->tags,
            'is_free' => $this->is_free,
            'is_public' => $this->is_public,
            'is_submitted' => $this->is_submitted,
            'download_count' => $this->download_count,
            'view_count' => $this->view_count,
            'user_cave' => $this->userCave == null ? false : true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        if($this->draft){
            $response["draft"] = $this->draft;
        }
        if($this->file){
            $response["source_code"] = $this->file;
        }

        return $response;
    }
}
