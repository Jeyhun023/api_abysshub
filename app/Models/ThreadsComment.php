<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadsComment extends Model
{
    use HasFactory;
    
    public $table = "threads_comments";
    
    protected $fillable = ['thread_id','user_id','content'];
    
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
