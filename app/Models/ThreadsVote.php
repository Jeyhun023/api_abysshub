<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadsVote extends Model
{
    use HasFactory;

    const VOTE_TYPE_SELECT = ['upvote'];
    
    public $table = "threads_vote";

    protected $fillable = ['user_id', 'thread_id', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}
