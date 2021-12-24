<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    public $table = "threads";

    protected $fillable = ['user_id', 'product_id', 'title','description','slug','content','tags','last_active_at', 'type'];
    protected $guarded = ['accepted_answer_id', 'closed_at', 'answer_count', 'comment_count', 'view_count', 'upvote']; 
    protected $casts = ['tags' => 'json'];
    protected $dates = ['last_active_at','closed_at'];

    public const THREAD_TYPE = [
        '1' => 'Question',
        '2' => 'Request',
        '3' => 'Discussion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function upvote()
    {
        return $this->hasMany(ThreadsVote::class)->where('type', 'upvote');
    }

    public function downvote()
    {
        return $this->hasMany(ThreadsVote::class)->where('type', 'downvote');
    }

    public function linked()
    {
        return $this->hasMany(ThreadLinkedProduct::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function userVotes()
    {
        return $this->hasOne(ThreadsVote::class)->where('user_id', auth()->guard('api')->user()?->id);
    }

    public function comments()
    {
        return $this->hasMany(ThreadsComment::class);
    }
}
