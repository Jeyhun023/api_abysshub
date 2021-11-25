<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = "products";

    protected $fillable = ['user_id', 'shop_id', 'file', 'name','slug','description','price', 'status'];
    protected $guarded = ['rate', 'download_count']; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function iterations()
    {
        return $this->hasMany(ProductIteration::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}