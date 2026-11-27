<?php

namespace App\Enums;

enum FulfillmentType: string
{
    case Pickup = 'pickup';
    case Delivery = 'delivery';
    case Digital = 'digital';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Pickup => 'Ambil Sendiri',
            self::Delivery => 'Diantar/Dikirim',
            self::Digital => 'Digital',
            self::Service => 'Jasa',
        };
    }
}
