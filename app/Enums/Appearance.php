<?php

namespace App\Enums;

enum Appearance: string
{
    case Light = 'light';
    case Dark = 'dark';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Light => __('Light'),
            self::Dark => __('Dark'),
            self::System => __('System'),
        };
    }

    public static function tryFromCookie(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }

    public static function resolve(?self $value): self
    {
        return $value ?? self::System;
    }
}
