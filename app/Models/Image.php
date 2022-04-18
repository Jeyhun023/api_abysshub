<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    public $table = "images";
    protected $fillable = ['imageable_id', 'imageable_type', 'title', 'path', 'order_id', 'is_active'];

    public function linkable()
    {
        return $this->morphTo();
    }

    public function getPathSrcAttribute()
    {
        return config('app.url').$this->path;
    }
}
