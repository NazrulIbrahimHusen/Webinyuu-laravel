<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case WaitingPayment = 'waiting_payment';
    case WaitingVerification = 'waiting_verification';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::WaitingPayment => 'Menunggu Pembayaran',
            self::WaitingVerification => 'Menunggu Verifikasi',
            self::Paid => 'Dibayar',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
