<?php

namespace App\Actions;

use App\Enums\DeviceType;

class GetDeviceAction
{
    /**
     * Create a new class instance.
     */
    public static function execute(): DeviceType
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua)) {
            return DeviceType::TABLET;
        }

        if (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $ua)) {
            return DeviceType::MOBILE;
        }

        return DeviceType::DESKTOP;
    }
}
