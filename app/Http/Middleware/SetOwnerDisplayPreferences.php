<?php

namespace App\Http\Middleware;

use App\Enums\Appearance;
use App\Enums\DisplayLanguage;
use App\Models\User;
use App\Support\OwnerDisplayPreferences;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetOwnerDisplayPreferences
{
    /**
     * @var array<int, Cookie>
     */
    private array $queuedCookies = [];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $displayLanguage = OwnerDisplayPreferences::resolveDisplayLanguage($request, $user);
        $appearance = OwnerDisplayPreferences::resolveAppearance($request, $user);

        if ($user !== null) {
            OwnerDisplayPreferences::persistDetectedLanguageForUser($user, $displayLanguage);
        }

        App::setLocale($displayLanguage->value);

        View::share([
            'resolvedDisplayLanguage' => $displayLanguage,
            'resolvedAppearance' => $appearance->value,
        ]);

        $this->queuePreferenceCookies($request, $user, $displayLanguage, $appearance);

        $response = $next($request);

        foreach ($this->queuedCookies as $cookie) {
            $response = $response->withCookie($cookie);
        }

        return $response;
    }

    private function queuePreferenceCookies(
        Request $request,
        ?User $user,
        DisplayLanguage $displayLanguage,
        Appearance $appearance,
    ): void {
        if ($request->cookie(OwnerDisplayPreferences::localeCookieName()) !== $displayLanguage->value) {
            $this->queuedCookies[] = OwnerDisplayPreferences::makeLocaleCookie($displayLanguage);
        }

        if ($request->cookie(OwnerDisplayPreferences::appearanceCookieName()) !== $appearance->value) {
            $this->queuedCookies[] = OwnerDisplayPreferences::makeAppearanceCookie($appearance);
        }

        if ($user !== null && $user->wasChanged('display_language')) {
            $this->queuedCookies[] = OwnerDisplayPreferences::makeLocaleCookie($displayLanguage);
        }
    }
}
