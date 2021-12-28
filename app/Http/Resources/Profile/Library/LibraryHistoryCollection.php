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
                'id' => $history->thread->id,
                'user' => new UserResource($history->thread->user),
                'title' => $history->thread->title,
                'slug' => $history->thread->slug,
                'description' => $history->thread->description,
                'tags' => $history->thread->tags,
                'accepted_answer_id' => $history->thread->accepted_answer_id,
                'answer_count' => $history->thread->answer_count,
                'comment_count' => $history->thread->comment_count,
                'view_count' => $history->thread->view_count,
                'upvote' => $history->thread->upvote_count,
                'downvote' => $history->thread->downvote_count,
                'type' => $history->thread->type,
                'last_active_at' => $history->thread->last_active_at,
                'closed_at' => $history->thread->closed_at,
                'created_at' => $history->thread->created_at->format('d/m/Y'),
                'updated_at' => $history->thread->updated_at->format('d/m/Y')
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
