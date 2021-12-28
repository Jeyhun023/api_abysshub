<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = "products";

    protected $fillable = ['user_id', 'shop_id', 'file', 'name','slug','description','price', 'status'];
    protected $casts = ['tags' => 'json'];
    protected $guarded = ['rate', 'download_count']; 

    public const PRODUCT_STATUS = [
        '0' => 'Plagiarism detected',
        '1' => 'Not plagiat',
        '2' => 'Submitted'
    ];

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
    
    public function userCave()
    {
        return $this->hasOne(Inventory::class)->where('user_id', auth()->guard('api')->user()?->id);
    }
}