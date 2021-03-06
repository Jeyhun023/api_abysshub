<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'fullname' => $this->fullname,
            'image' => $this->image,
            'description' => $this->description,
            'skills' => $this->skills, 
            'email' => $this->email,
            'isVerified' => ($this->email_verified_at == null) ? false : true,
        ];
    }
}
