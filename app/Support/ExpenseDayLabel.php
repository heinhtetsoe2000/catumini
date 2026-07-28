<?php

namespace App\Support;

use Carbon\Carbon;

class ExpenseDayLabel
{
    public static function for(Carbon $date): string
    {
        $date = $date->copy()->timezone('Asia/Yangon')->startOfDay();
        $today = now('Asia/Yangon')->startOfDay();

        if ($date->equalTo($today)) {
            return __('Today');
        }

        if ($date->equalTo($today->copy()->subDay())) {
            return __('Yesterday');
        }

        return self::format($date, 'group');
    }

    public static function forDateString(string $dateString): string
    {
        return self::for(Carbon::parse($dateString, 'Asia/Yangon'));
    }

    public static function forHeader(Carbon $date): string
    {
        $date = $date->copy()->timezone('Asia/Yangon');

        if ($date->isToday()) {
            return __('Today');
        }

        return self::format($date, 'header');
    }

    public static function forMonth(Carbon $date): string
    {
        return self::format($date, 'month');
    }

    public static function forProfile(Carbon $date): string
    {
        return self::format($date, 'profile');
    }

    private static function format(Carbon $date, string $context): string
    {
        if (app()->getLocale() === 'my') {
            return self::formatBurmese($date, $context);
        }

        $date = $date->locale(app()->getLocale());

        return match ($context) {
            'group' => $date->translatedFormat('D M d'),
            'header' => $date->translatedFormat('M d'),
            'month' => $date->translatedFormat('M Y'),
            'profile' => $date->translatedFormat('j F Y'),
        };
    }

    private static function formatBurmese(Carbon $date, string $context): string
    {
        $formatter = new \IntlDateFormatter(
            'my_MM',
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            $date->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN,
            match ($context) {
                'group' => 'MMMM d',
                'header' => 'MMMM d',
                'month' => 'MMMM Y',
                'profile' => 'MMMM d, Y',
            },
        );

        $formatted = $formatter->format($date->getTimestamp());

        return $formatted !== false ? $formatted : $date->locale('my')->translatedFormat('j F');
    }
}
