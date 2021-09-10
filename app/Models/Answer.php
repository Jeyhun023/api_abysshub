<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public $table = "answers";

    protected $fillable = ['user_id', 'thread_id','content'];
    protected $guarded = ['upvote', 'comment_count']; 

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
        return $this->hasOne(AnswersVote::class)->where('user_id', auth()->guard('api')->user()?->id);
    }

    public function comments()
    {
        return $this->hasMany(AnswersComment::class);
    }
}
