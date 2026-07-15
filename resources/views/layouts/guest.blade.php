<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600|source-serif-4:600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="font-sans text-ink antialiased dark:text-ink-invert">
        <div class="flex min-h-screen flex-col items-center bg-paper pt-6 sm:justify-center sm:pt-0 dark:bg-paper-dark">
            <div>
                <x-application-wordmark :href="url('/')" class="text-3xl" />
            </div>

            <div class="mt-8 w-full overflow-hidden border border-ink/10 bg-paper-elevated px-6 py-5 sm:max-w-md sm:rounded-lg dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
                {{ $slot }}
            </div>
        </div>

        @fluxScripts
    </body>
</html>
