<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Store\ProductResource;

class CaveCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($cave) {
            return [
                'id' => $cave->id,
                'user' => new UserResource($cave->user),
                'product' => $cave->product,
                'type' => $cave->type,
                'created_at' => $cave->created_at->format('d/m/Y'),
                'updated_at' => $cave->updated_at->format('d/m/Y')
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
