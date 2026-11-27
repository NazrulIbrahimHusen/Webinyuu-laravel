<?php

namespace App\Support;

class Phone
{
    /** 0812... -> 62812... untuk tautan wa.me */
    public static function intl(?string $n): string
    {
        $d = preg_replace('/\D+/', '', (string) $n);
        if (str_starts_with($d, '0')) {
            $d = '62'.substr($d, 1);
        }

        return $d;
    }

    public static function wa(?string $n, string $text = ''): string
    {
        return 'https://wa.me/'.self::intl($n).($text !== '' ? '?text='.rawurlencode($text) : '');
    }
}
