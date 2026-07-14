<?php

namespace App\Enums;

enum DeviceType: string
{
    case TABLET = 'tablet';
    case MOBILE = 'mobile';
    case DESKTOP = 'desktop';

    public function isMobile(): bool
    {
        return $this === self::MOBILE;
    }

    public function isTablet(): bool
    {
        return $this === self::TABLET;
    }

    public function isDesktop(): bool
    {
        return $this === self::DESKTOP;
    }
}
