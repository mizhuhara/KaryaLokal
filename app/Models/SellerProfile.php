<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'shop_name', 'slug', 'description', 'address',
    'province', 'city', 'district', 'latitude', 'longitude',
    'operating_hours', 'pickup_available', 'delivery_available',
    'custom_order_available', 'is_verified', 'verified_at',
])]
class SellerProfile extends Model
{
    use HasFactory;

    protected $casts = [
        'pickup_available' => 'bool',
        'delivery_available' => 'bool',
        'custom_order_available' => 'bool',
        'is_verified' => 'bool',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::saving(function (SellerProfile $s) {
            if (empty($s->slug)) {
                $s->slug = Str::slug($s->shop_name) . '-' . Str::random(5);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_profile_id');
    }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
