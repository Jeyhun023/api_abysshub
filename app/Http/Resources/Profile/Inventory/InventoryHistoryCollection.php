<?php

namespace App\Http\Resources\Profile\Inventory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class InventoryHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($history) {
            return [
                'id' => $history->product->id,
                'user' => new UserResource($history->product->user),
                'name' => $history->product->name,
                'slug' => $history->product->slug,
                'description' => $history->product->description,
                'price' => $history->product->price,
                'rate' => $history->product->rate,
                'tags' => $history->product->tags,
                'download_count' => $history->product->download_count,
                'created_at' => $history->product->created_at->format('d/m/Y'),
                'updated_at' => $history->product->updated_at->format('d/m/Y')
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
