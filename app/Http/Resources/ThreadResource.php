<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\AnswerCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'category' => new CategoryResource($this->category),
            'answers' => new AnswerCollection($this->answers),
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'tags' => $this->tags,
            'accepted_answer_id' => $this->accepted_answer_id,
            'answer_count' => $this->answer_count,
            'comment_count' => $this->comment_count,
            'view_count' => $this->view_count,
            'score' => $this->score,
            'last_active_at' => $this->last_active_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y')
        ];
    }
}