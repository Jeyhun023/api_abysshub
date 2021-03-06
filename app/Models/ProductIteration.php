<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIteration extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'name', 'note','file'];
    protected $guarded = ['rate', 'download_count', 'status'];

    public const ITERATION_STATUS = [
        '0' => 'Plagiarism detected',
        '1' => 'Submitted'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
