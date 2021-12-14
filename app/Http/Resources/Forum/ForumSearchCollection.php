<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Thread;

class ForumSearchCollection extends ResourceCollection
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
                'content' => $result['_source']['content'],
                'answer_count' => $result['_source']['answer_count'],
                'user' => (isset($result['_source']['user'])) ? $result['_source']['user'] : null,
                'product' => (isset($result['_source']['product'])) ? $result['_source']['product'] : null,
                'tags' => $result['_source']['tags'],
                'type' => (isset($result['_source']['type'])) ? Thread::THREAD_TYPE[$result['_source']['type']] : 'Question',
                'created_at' => (isset($result['_source']['created_at'])) ? $result['_source']['created_at'] : null,
                'updated_at' => (isset($result['_source']['updated_at'])) ? $result['_source']['updated_at'] : null,
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
