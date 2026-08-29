<?php

namespace App\Enums;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Buyer => 'Pembeli',
            self::Seller => 'Penjual',
            self::Admin => 'Admin',
        };
    }

    public function homeRoute(): string
    {
        return match ($this) {
            self::Admin => route('admin.dashboard', absolute: false),
            self::Seller => route('seller.dashboard', absolute: false),
            default => route('home', absolute: false),
        };
    }
}
