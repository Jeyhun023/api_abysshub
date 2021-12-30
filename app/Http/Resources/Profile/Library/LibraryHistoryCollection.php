<?php

namespace App\Http\Resources\Profile\Library;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Auth\UserResource;

class LibraryHistoryCollection extends ResourceCollection
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
                'title' => $history->subject->title,
                'slug' => $history->subject->slug,
                'description' => $history->subject->description,
                'tags' => $history->subject->tags,
                'accepted_answer_id' => $history->subject->accepted_answer_id,
                'answer_count' => $history->subject->answer_count,
                'comment_count' => $history->subject->comment_count,
                'view_count' => $history->subject->view_count,
                'upvote' => $history->subject->upvote_count,
                'downvote' => $history->subject->downvote_count,
                'type' => $history->subject->type,
                'last_active_at' => $history->subject->last_active_at,
                'closed_at' => $history->subject->closed_at,
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
