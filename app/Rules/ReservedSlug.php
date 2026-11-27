<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReservedSlug implements ValidationRule
{
    public const RESERVED = [
        'login', 'logout', 'register', 'dashboard', 'admin', 'api', 'storage', 'katalog',
        'pesanan', 'password', 'email', 'forgot-password', 'reset-password', 'verify-email',
        'profile', 'about', 'pricing', 'help', 'support', 'terms', 'privacy', 'webinyuu',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower((string) $value), self::RESERVED, true)) {
            $fail('URL ini tidak tersedia, silakan pilih yang lain.');
        }
    }
}
