<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class LinkedProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($linked) {
            return [
                'id' => $linked->product->id,
                'user' => new UserResource($linked->product->user),
                'name' => $linked->product->name,
                'slug' => $linked->product->slug,
                'description' => $linked->product->description,
                'price' => $linked->product->price,
                'tags' => $linked->product->tags,
                'rate' => $linked->product->rate,
                'view_count' => $linked->product->view_count,
                'download_count' => $linked->product->download_count,
                'created_at' => $linked->product->created_at->format('d/m/Y'),
                'updated_at' => $linked->product->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }

    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        unset($jsonResponse['links'],$jsonResponse['meta']['links'],$jsonResponse['meta']['path']);
        $response->setContent(json_encode($jsonResponse));
    }
}
