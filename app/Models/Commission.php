<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'order_id',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'seller_payout',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'seller_payout' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function calculateForOrder(Order $order): array
    {
        $rate = (float) config('karyalokal.commission_rate', 5.00);
        $commission = $order->total_amount * ($rate / 100);

        return [
            'order_amount' => $order->total_amount,
            'commission_rate' => $rate,
            'commission_amount' => $commission,
            'seller_payout' => $order->total_amount - $commission,
        ];
    }
}
