<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerLinkedProduct extends Model
{
    use HasFactory;
    
    public $table = "answer_linked_products";
    
    protected $fillable = ['answer_id','product_id'];
    
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function thread()
    {
        return $this->belongsToThrough(Answer::class, Thread::class);
    }
}
