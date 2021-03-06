<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedProduct extends Model
{
    use HasFactory;

    public $table = "linked_products";
    protected $fillable = ['linkable_id', 'linkable_type', 'product_id'];

    public function linkable()
    {
        return $this->morphTo();
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'linkable_id')->where('linkable_type', 'App\Models\Answer');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
