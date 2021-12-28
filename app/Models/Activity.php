<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public $table = "activity_log";

    public function product()
    {
        return $this->belongsTo(Product::class, 'subject_id', 'id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'subject_id', 'id');
    }
}
