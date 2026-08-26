<?php

namespace App\Models;

use App\Notifications\OrderStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'seller_profile_id',
        'order_number',
        'status',
        'total_amount',
        'notes',
        'delivery_type',
        'delivery_address',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
            }
        });

        static::updated(function (Order $order) {
            if ($order->wasChanged('status')) {
                $buyer = $order->user;

                $statusMessages = [
                    'confirmed' => 'Pesanan dikonfirmasi penjual',
                    'processing' => 'Pesanan sedang diproses',
                    'ready' => 'Pesanan siap diambil/dikirim',
                    'shipped' => 'Pesanan dalam perjalanan',
                    'completed' => 'Pesanan selesai',
                    'cancelled' => 'Pesanan dibatalkan',
                    'rejected' => 'Pesanan ditolak penjual',
                ];

                if (isset($statusMessages[$order->status])) {
                    $buyer->createNotification(
                        'order',
                        'Status Pesanan Diperbarui',
                        $order->order_number . ': ' . $statusMessages[$order->status],
                        $order
                    );

                    // Send email notification
                    $buyer->notify(new OrderStatusChanged($order));
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_profile_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
