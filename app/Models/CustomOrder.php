<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomOrder extends Model
{
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'title',
        'description',
        'budget',
        'deadline',
        'status',
        'quoted_price',
        'seller_notes',
        'completed_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'quoted_price' => 'decimal:2',
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CustomOrderImage::class);
    }
}
