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
                'user' => new UserResource($answer->user),
                'content' => $answer->content,
                'upvote' => $answer->upvote,
                'comment_count' => $answer->comment_count,
                'user_votes' => $answer->userVotes,
                'created_at' => $answer->created_at->format('d/m/Y'),
                'updated_at' => $answer->updated_at->format('d/m/Y'),
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
