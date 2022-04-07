<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public $table = "products";

    protected $fillable = ['user_id', 'shop_id', 'draft', 'file', 'name', 'price', 
        'slug', 'description', 'is_free', 'is_plagiat', 'is_submitted', 'is_public', 'tags', 'extension'];
    protected $casts = ['tags' => 'json'];
    protected $guarded = ['rate', 'download_count']; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function iterations()
    {
        return $this->hasMany(ProductIteration::class);
    }

    public function linkedProducts()
    {
        return $this->hasMany(LinkedProduct::class);
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

    public function scopeSubmitted(Builder $query): Builder
    {
        $query = $query->where('is_submitted', true);
        return $query;
    }

    public function scopeFree(Builder $query): Builder
    {
        $query = $query->where('is_free', true);
        return $query;
    }

    public function scopePublic(Builder $query): Builder
    {
        $query = $query->where('is_public', true);
        return $query;
    }
}