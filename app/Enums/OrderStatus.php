<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Ready = 'ready';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Confirmed => 'Dikonfirmasi',
            self::Processing => 'Diproses',
            self::Ready => 'Siap',
            self::Shipped => 'Dikirim / Pickup',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::Rejected => 'Ditolak',
        };
    }
}
