<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->used_count >= $this->usage_limit) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    public function calculateDiscount($orderAmount): float
    {
        if ($orderAmount < $this->min_order) return 0;

        if ($this->type === 'percentage') {
            $discount = $orderAmount * ($this->value / 100);
            return $this->max_discount ? min($discount, $this->max_discount) : $discount;
        }

        return min($this->value, $orderAmount);
    }

    public function apply(): void
    {
        $this->increment('used_count');
    }
}
