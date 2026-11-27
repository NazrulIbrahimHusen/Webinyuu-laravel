<?php

namespace App\Enums;

enum ShippingMode: string
{
    case Auto = 'auto';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Auto => 'Otomatis (RajaOngkir)',
            self::Manual => 'Manual (Zona)',
        };
    }
}
