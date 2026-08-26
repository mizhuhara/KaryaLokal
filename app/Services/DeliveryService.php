<?php

namespace App\Services;

class DeliveryService
{
    public const CARRIERS = [
        'jne' => ['name' => 'JNE', 'estimates' => '1-3 hari', 'logo' => '📦'],
        'jnt' => ['name' => 'J&T Express', 'estimates' => '1-3 hari', 'logo' => '📦'],
        'sicepat' => ['name' => 'SiCepat', 'estimates' => '1-2 hari', 'logo' => '📦'],
        'gojek' => ['name' => 'GoSend', 'estimates' => 'Hari yang sama', 'logo' => '🏍️'],
        'grab' => ['name' => 'GrabExpress', 'estimates' => 'Hari yang sama', 'logo' => '🏍️'],
    ];

    public static function getCarriers(): array
    {
        return self::CARRIERS;
    }

    public static function getCarrier($code): ?array
    {
        return self::CARRIERS[$code] ?? null;
    }

    public static function estimateCost(string $carrier, float $weight, string $origin, string $destination): array
    {
        $baseCosts = [
            'jne' => 15000,
            'jnt' => 13000,
            'sicepat' => 14000,
            'gojek' => 25000,
            'grab' => 25000,
        ];

        $baseCost = $baseCosts[$carrier] ?? 15000;
        $weightCost = max(0, ($weight - 1)) * 3000;

        return [
            'carrier' => $carrier,
            'cost' => $baseCost + $weightCost,
            'estimates' => self::CARRIERS[$carrier]['estimates'] ?? '1-3 hari',
        ];
    }
}