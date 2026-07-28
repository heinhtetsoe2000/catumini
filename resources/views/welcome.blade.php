<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <x-locale-fonts />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
        <x-resolved-appearance />
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-invert">
        <main class="flex min-h-screen flex-col items-center justify-center bg-white px-6 dark:bg-black">
            <div class="w-full max-w-sm text-center">
                <img src="{{ asset('logo-with-bg.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-4 rounded-xl">
                <h1 class="gate-enter">
                    <x-application-wordmark class="text-4xl sm:text-5xl" />
                </h1>

                <p class="gate-enter-delay-1 mt-4 text-base dark:text-ink-soft">
                    {{ __('Your personal expense ledger.') }}
                </p>

                <div class="gate-enter-delay-2 mt-6 flex justify-center">
                    <x-display-language-toggle />
                </div>

                <div class="gate-enter-delay-2 mt-6 flex justify-center">
                    <flux:button :href="route('login')" variant="primary" color="zinc" class="w-full sm:w-auto">
                        {{ __('Login') }}
                    </flux:button>
                </div>
            </div>
        </main>

        @fluxScripts
    </body>
</html>
