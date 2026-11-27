<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case NotShipped = 'not_shipped';
    case Shipped = 'shipped';
    case Received = 'received';

    public function label(): string
    {
        return match ($this) {
            self::NotShipped => 'Belum Dikirim',
            self::Shipped => 'Dikirim',
            self::Received => 'Diterima',
        };
    }
}
