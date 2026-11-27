<?php

namespace App\Enums;

enum UserRole: string
{
    case Seller = 'seller';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Seller => 'Seller',
            self::Admin => 'Admin',
        };
    }
}
