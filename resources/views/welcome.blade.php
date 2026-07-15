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
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-invert">
        <main class="min-h-screen flex flex-col items-center justify-center px-6 bg-paper dark:bg-paper-dark">
            <div class="w-full max-w-sm text-center">
                <h1 class="gate-enter">
                    <x-application-wordmark class="text-4xl sm:text-5xl" />
                </h1>

                <p class="gate-enter-delay-1 mt-4 text-base text-ink-muted dark:text-ink-soft">
                    Your personal expense ledger.
                </p>

                <a
                    href="{{ route('login') }}"
                    class="gate-enter-delay-2 mt-10 inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 rounded-md bg-accent text-white font-medium text-sm tracking-wide hover:bg-accent-hover dark:bg-accent-dark dark:text-paper-dark dark:hover:bg-accent-dark-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-paper dark:focus:ring-offset-paper-dark transition"
                >
                    Login
                </a>
            </div>
        </main>
    </body>
</html>
