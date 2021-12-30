<?php

namespace App\Http\Resources\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

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
                'linked_products' => $answer->linked,
                'content' => $answer->content,
                'upvote' => $answer->upvote,
                'downvote' => $answer->downvote,
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
