<?php

namespace App\Listeners;

use App\Support\OwnerDisplayPreferences;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cookie;

class SyncOwnerDisplayPreferencesOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        foreach (OwnerDisplayPreferences::syncCookiesFromUser($event->user) as $cookie) {
            Cookie::queue($cookie);
        }
    }
}
