<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model // Renamed from 'products' to 'Product'
{
    use HasFactory;

    protected $fillable = [
        'title',
        'desc',
        'image',
        'rating',
        'category',
        'release_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
