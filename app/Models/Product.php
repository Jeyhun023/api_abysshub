<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = "products";

    protected $fillable = ['parent_id', 'user_id', 'category_id', 'shop_id', 'file', 'name','slug','source_code','description','price'];
    protected $guarded = ['rate', 'view_count', 'download_count']; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function iterations()
    {
        return $this->hasMany(Product::class, 'parent_id', 'id');
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
