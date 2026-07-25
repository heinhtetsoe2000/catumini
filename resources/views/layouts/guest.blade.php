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
    <body class="font-sans text-black dark:text-white antialiased">
        <div class="flex h-screen items-center justify-center bg-white pt-6 sm:justify-center sm:pt-0 dark:bg-black">
            <div>
                <x-application-wordmark :href="url('/')" class="text-3xl mb-4 block text-center" />
                <flux:card class="mx-auto m-4 w-xs sm:w-96 max-w-7xl px-4 sm:px-6 lg:px-8 bg-white dark:bg-black">
                    {{ $slot }}
                </flux:card>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
