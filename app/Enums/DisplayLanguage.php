<?php

namespace App\Enums;

enum DisplayLanguage: string
{
    case En = 'en';
    case My = 'my';

    public function label(): string
    {
        return match ($this) {
            self::En => 'EN',
            self::My => 'MY',
        };
    }

    public static function tryFromCookie(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}
