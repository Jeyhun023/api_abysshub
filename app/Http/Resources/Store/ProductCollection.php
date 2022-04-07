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
                'user' => new UserResource($product->whenLoaded('user')),
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'rate' => $product->rate,
                'tags' => $product->tags,
                'is_public' => $product->is_public,
                'linked_products_count' => $product->linkedProducts_count,
                'iterations_count' => $product->iterations_count,
                'download_count' => $product->download_count,
                'created_at' => $product->created_at->format('d/m/Y'),
                'updated_at' => $product->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
