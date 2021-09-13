<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    public $table = "threads";

    protected $fillable = ['user_id', 'category_id','title','slug','content','tags','last_active_at'];
    protected $guarded = ['accepted_answer_id', 'closed_at', 'answer_count', 'comment_count', 'view_count', 'upvote']; 

    protected $dates = ['last_active_at','closed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->limit(5);
    }

    public function userVotes()
    {
        return $this->hasOne(ThreadsVote::class)->where('user_id', auth()->guard('api')->user()?->id);
    }

    public function comments()
    {
        return $this->hasMany(ThreadsComment::class)->orderBy('id','DESC');
    }
}
