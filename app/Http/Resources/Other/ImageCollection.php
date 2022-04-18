<?php

namespace App\Http\Resources\Other;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ImageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($image) {
            return [
                'id' => $image->id,
                'title' => $image->title,
                'path' => $image->path_src,
                'order' => $image->order_id,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
