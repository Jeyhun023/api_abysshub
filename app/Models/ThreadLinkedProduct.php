<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadLinkedProduct extends Model
{
    use HasFactory;
    
    public $table = "thread_linked_products";
    
    protected $fillable = ['answer_id','thread_id'];
    
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
