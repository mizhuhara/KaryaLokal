<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'location', 'quote', 'rating', 'is_active'];

    protected $casts = [
        'is_active' => 'bool',
        'rating' => 'integer',
    ];
}
