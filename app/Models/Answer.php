<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public $table = "answers";

    protected $fillable = ['user_id', 'thread_id','content'];
    protected $guarded = ['comment_count', 'upvote', 'downvote']; 

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userVotes()
    {
        return $this->morphOne(Vote::class, 'voteable')->where('user_id', auth()->guard('api')->user()?->id);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function linked()
    {
        return $this->morphMany(LinkedProduct::class, 'linkable');
    }
}
 