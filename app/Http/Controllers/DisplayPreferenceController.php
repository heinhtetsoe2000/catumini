<?php

namespace App\Http\Controllers;

use App\Enums\Appearance;
use App\Enums\DisplayLanguage;
use App\Support\OwnerDisplayPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DisplayPreferenceController extends Controller
{
    public function updateDisplayLanguage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_language' => ['required', Rule::enum(DisplayLanguage::class)],
        ]);

        $language = DisplayLanguage::from($validated['display_language']);

        if ($request->user() !== null) {
            $request->user()->update([
                'display_language' => $language,
            ]);
        }

        return redirect()
            ->back()
            ->withCookie(OwnerDisplayPreferences::makeLocaleCookie($language));
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'appearance' => ['required', Rule::enum(Appearance::class)],
        ]);

        $appearance = Appearance::from($validated['appearance']);

        $request->user()?->update([
            'appearance' => $appearance,
        ]);

        return redirect()
            ->back()
            ->withCookie(OwnerDisplayPreferences::makeAppearanceCookie($appearance));
    }
}
