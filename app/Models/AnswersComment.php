<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswersComment extends Model
{
    use HasFactory;
    
    public $table = "answers_comments";
    
    protected $fillable = ['answer_id','user_id','content'];
    
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
