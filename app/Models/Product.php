<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function getImageUrlAttribute($value)
    {
        return $value ? asset($value) : null;
    }
}
