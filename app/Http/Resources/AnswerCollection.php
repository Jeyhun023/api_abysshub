<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($answer) {
            return [
                'id' => $answer->id,
                'parent_id' => $answer->parent_id,
                'user' => new UserResource($answer->user),
                'content' => $answer->content,
                'score' => $answer->score,
                'created_at' => $answer->created_at->format('d/m/Y'),
                'updated_at' => $answer->updated_at->format('d/m/Y')
            ];
        })->toArray();
        return parent::toArray($request);
    }
}
