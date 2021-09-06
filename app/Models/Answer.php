<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public $table = "answers";

    protected $fillable = ['thread_id','user_id','parent_id','content'];
    protected $guarded = ['score']; 

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
