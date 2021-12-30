<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $table = "notifications";

    protected $fillable = ['user_id', 'type', 'subject_type', 'subject_id', 'data', 'read_at']; 
}
