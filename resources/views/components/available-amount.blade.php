@props([
    'amount',
])

<span @class([
    'font-bold',
    'text-red-600 dark:text-red-400' => $amount < 0,
])>{{ number_format($amount) }} {{ __('Ks') }}</span>
