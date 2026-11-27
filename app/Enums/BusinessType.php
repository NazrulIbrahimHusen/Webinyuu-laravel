<?php

namespace App\Enums;

enum BusinessType: string
{
    case Fnb = 'fnb';
    case Physical = 'physical';
    case Digital = 'digital';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Fnb => 'F&B',
            self::Physical => 'Produk Fisik',
            self::Digital => 'Produk Digital',
            self::Service => 'Jasa',
        };
    }
}
