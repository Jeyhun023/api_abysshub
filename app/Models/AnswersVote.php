<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswersVote extends Model
{
    use HasFactory;

    const VOTE_TYPE_SELECT = ['upwote'];

    public $table = "answers_vote";

    protected $fillable = ['user_id', 'answer_id', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
}
