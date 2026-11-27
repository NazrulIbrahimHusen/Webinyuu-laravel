<?php

namespace App\Enums;

enum PlanCode: string
{
    case Basic = 'basic';
    case Starter = 'starter';
    case Pro = 'pro';

    public function label(): string
    {
        return match ($this) {
            self::Basic => 'Basic',
            self::Starter => 'Starter',
            self::Pro => 'Pro',
        };
    }
}
