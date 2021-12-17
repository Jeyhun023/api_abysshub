<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($product) {
            return [
                'id' => $product->id,
                'user' => new UserResource($product->user),
                'iterations' => $product->whenLoaded('iterations', new ProductCollection($product->iterations), null),
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'rate' => $product->rate,
                'tags' => $product->tags,
                'download_count' => $product->download_count,
                'created_at' => $product->created_at->format('d/m/Y'),
                'updated_at' => $product->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
