<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOrderImage extends Model
{
    protected $fillable = ['custom_order_id', 'image_path'];

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }
}
