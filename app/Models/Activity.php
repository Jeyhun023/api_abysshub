<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    public $table = "activity_log";
    protected $fillable = ['log_name', 'description','properties'];
}