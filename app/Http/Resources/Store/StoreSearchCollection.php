<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreSearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($result) {
            return [
                'id' => $result['_id'],
                'score' => $result['_score'],
                'title' => $result['_source']['title'],
                'slug' => $result['_source']['slug'],
                'description' => $result['_source']['description'],
                'download_count' => $result['_source']['download_count'],
                'price' => $result['_source']['price'],
                'rate' => $result['_source']['rate'],
                'shop' => (isset($result['_source']['shop'])) ? $result['_source']['shop'] : null,
                'user' => (isset($result['_source']['user'])) ? $result['_source']['user'] : null,
                'tags' => $result['_source']['tags'],
                'created_at' => (isset($result['_source']['created_at'])) ? $result['_source']['created_at'] : null,
                'updated_at' => (isset($result['_source']['updated_at'])) ? $result['_source']['updated_at'] : null,
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
