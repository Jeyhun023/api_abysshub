<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    const VOTE_TYPE_SELECT = ['upvote', 'downvote'];
    
    public $table = "votes";
    protected $fillable = ['user_id', 'voteable_type', 'voteable_id', 'type'];

    public function voteable()
    {
        return $this->morphTo();
    }
}
