<?php

use App\Support\ExpenseDayLabel;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

it('formats english group labels with abbreviated weekday and month', function () {
    App::setLocale('en');

    $label = ExpenseDayLabel::for(Carbon::parse('2026-07-25', 'Asia/Yangon'));

    expect($label)->toBe('Sat Jul 25');
});

it('formats burmese group labels with burmese numerals and full month name', function () {
    App::setLocale('my');

    $label = ExpenseDayLabel::for(Carbon::parse('2026-07-25', 'Asia/Yangon'));

    expect($label)->toBe('ဇူလိုင် ၂၅');
});

it('formats burmese header labels with burmese numerals and full month name', function () {
    App::setLocale('my');

    $label = ExpenseDayLabel::forHeader(Carbon::parse('2026-07-25', 'Asia/Yangon'));

    expect($label)->toBe('ဇူလိုင် ၂၅');
});
