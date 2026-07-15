<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600|source-serif-4:600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-invert">
        <main class="flex min-h-screen flex-col items-center justify-center bg-paper px-6 dark:bg-paper-dark">
            <div class="w-full max-w-sm text-center">
                <h1 class="gate-enter">
                    <x-application-wordmark class="text-4xl sm:text-5xl" />
                </h1>

                <p class="gate-enter-delay-1 mt-4 text-base text-ink-muted dark:text-ink-soft">
                    Your personal expense ledger.
                </p>

                <div class="gate-enter-delay-2 mt-10 flex justify-center">
                    <flux:button :href="route('login')" variant="primary" class="w-full sm:w-auto">
                        Login
                    </flux:button>
                </div>
            </div>
        </main>

        @fluxScripts
    </body>
</html>
