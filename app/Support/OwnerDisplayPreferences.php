<?php

namespace App\Support;

use App\Enums\Appearance;
use App\Enums\DisplayLanguage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class OwnerDisplayPreferences
{
    public static function localeCookieName(): string
    {
        return config('display.locale_cookie');
    }

    public static function appearanceCookieName(): string
    {
        return config('display.appearance_cookie');
    }

    public static function cookieLifetime(): int
    {
        return (int) config('display.cookie_lifetime_minutes');
    }

    public static function makeLocaleCookie(DisplayLanguage $language): Cookie
    {
        return cookie(
            self::localeCookieName(),
            $language->value,
            self::cookieLifetime(),
            '/',
            null,
            request()->isSecure(),
            true,
            false,
            Cookie::SAMESITE_LAX
        );
    }

    public static function makeAppearanceCookie(Appearance $appearance): Cookie
    {
        return cookie(
            self::appearanceCookieName(),
            $appearance->value,
            self::cookieLifetime(),
            '/',
            null,
            request()->isSecure(),
            true,
            false,
            Cookie::SAMESITE_LAX
        );
    }

    public static function detectBrowserLanguage(Request $request): DisplayLanguage
    {
        $acceptLanguage = strtolower($request->header('Accept-Language', ''));

        if (str_contains($acceptLanguage, 'my') || str_contains($acceptLanguage, 'my-mm')) {
            return DisplayLanguage::My;
        }

        return DisplayLanguage::En;
    }

    public static function resolveDisplayLanguage(Request $request, ?User $user = null): DisplayLanguage
    {
        if ($user?->display_language instanceof DisplayLanguage) {
            return $user->display_language;
        }

        if ($user !== null && is_string($user->display_language)) {
            $fromUser = DisplayLanguage::tryFrom($user->display_language);

            if ($fromUser !== null) {
                return $fromUser;
            }
        }

        $fromCookie = DisplayLanguage::tryFromCookie($request->cookie(self::localeCookieName()));

        if ($fromCookie !== null) {
            return $fromCookie;
        }

        return self::detectBrowserLanguage($request);
    }

    public static function resolveAppearance(Request $request, ?User $user = null): Appearance
    {
        if ($user?->appearance instanceof Appearance) {
            return Appearance::resolve($user->appearance);
        }

        if ($user !== null && is_string($user->appearance)) {
            $fromUser = Appearance::tryFrom($user->appearance);

            if ($fromUser !== null) {
                return $fromUser;
            }
        }

        $fromCookie = Appearance::tryFromCookie($request->cookie(self::appearanceCookieName()));

        return Appearance::resolve($fromCookie);
    }

    public static function persistDetectedLanguageForUser(User $user, DisplayLanguage $language): void
    {
        if ($user->display_language !== null) {
            return;
        }

        $user->forceFill(['display_language' => $language])->save();
    }

    public static function syncCookiesFromUser(User $user): array
    {
        $cookies = [];

        $language = $user->display_language instanceof DisplayLanguage
            ? $user->display_language
            : DisplayLanguage::tryFrom((string) $user->display_language);

        if ($language !== null) {
            $cookies[] = self::makeLocaleCookie($language);
        }

        $appearance = $user->appearance instanceof Appearance
            ? $user->appearance
            : Appearance::tryFrom((string) $user->appearance);

        $cookies[] = self::makeAppearanceCookie(Appearance::resolve($appearance));

        return $cookies;
    }

    public static function cookieExpiry(): Carbon
    {
        return now()->addMinutes(self::cookieLifetime());
    }
}
