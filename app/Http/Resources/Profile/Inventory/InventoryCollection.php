<?php

namespace App\Http\Resources\Profile\Inventory;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Store\ProductResource;

class InventoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($inventory) {
            return [
                'id' => $inventory->id,
                'product' => $inventory->product,
                'type' => $inventory->type,
                'created_at' => $inventory->created_at->format('d/m/Y'),
                'updated_at' => $inventory->updated_at->format('d/m/Y')
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
