<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Other\CategoryResource;

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
                'category' => new CategoryResource($product->category),
                'iterations' => $product->whenLoaded('iterations', new ProductCollection($product->iterations), null),
                'name' => $product->name,
                'slug' => $product->slug,
                'source_code' => $product->source_code,
                'description' => $product->description,
                'price' => $product->price,
                'rate' => $product->rate,
                'view_count' => $product->view_count,
                'download_count' => $product->download_count,
                'created_at' => $product->created_at->format('d/m/Y'),
                'updated_at' => $product->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
