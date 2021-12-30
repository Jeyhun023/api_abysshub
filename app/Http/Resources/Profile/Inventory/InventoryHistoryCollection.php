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
                'id' => $history->subject->id,
                'user' => new UserResource($history->subject->user),
                'name' => $history->subject->name,
                'slug' => $history->subject->slug,
                'description' => $history->subject->description,
                'price' => $history->subject->price,
                'rate' => $history->subject->rate,
                'tags' => $history->subject->tags,
                'download_count' => $history->subject->download_count,
                'created_at' => $history->subject->created_at->format('d/m/Y'),
                'updated_at' => $history->subject->updated_at->format('d/m/Y')
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
