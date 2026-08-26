<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'seller_profile_id',
        'plan',
        'price',
        'product_limit',
        'priority_listing',
        'analytics_access',
        'verified_badge',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'product_limit' => 'integer',
        'priority_listing' => 'boolean',
        'analytics_access' => 'boolean',
        'verified_badge' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public const PLANS = [
        'basic' => [
            'name' => 'Basic',
            'price' => 0,
            'product_limit' => 10,
            'priority_listing' => false,
            'analytics_access' => false,
            'verified_badge' => false,
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 99000,
            'product_limit' => 50,
            'priority_listing' => true,
            'analytics_access' => true,
            'verified_badge' => true,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 249000,
            'product_limit' => -1,
            'priority_listing' => true,
            'analytics_access' => true,
            'verified_badge' => true,
        ],
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_profile_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function getPlanConfig(): array
    {
        return self::PLANS[$this->plan] ?? self::PLANS['basic'];
    }
}