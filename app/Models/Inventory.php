<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    public $table = "inventories";

    protected $fillable = ['user_id', 'product_id', 'type']; 
    
    public const CAVE_TYPE = [
        '1' => 'Add Cave',
        '2' => 'Empty',
        '3' => 'Empty'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
