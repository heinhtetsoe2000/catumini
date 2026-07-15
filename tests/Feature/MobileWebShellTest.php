<?php

use App\Models\User;

test('today page does not render native edge components', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('native:top-bar', false);
    $response->assertDontSee('native:bottom-nav', false);
    $response->assertSee(route('add-expense'), false);
    $response->assertSee('Add Expense', false);
    $response->assertSee(__('Today'), false);
    $response->assertSee(__('Monthly'), false);
    $response->assertSee(__('Profile'), false);
    $response->assertSee(config('app.name'), false);
    $response->assertDontSee('viewBox="0 0 316 316"', false);
    $response->assertSee('data-flux-navbar', false);
});

test('add expense is only via the Today CTA not a top-nav peer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect(preg_match_all('#href="[^"]*add-expense[^"]*"#', $html))->toBe(1)
        ->and($html)->toContain('Add Expense')
        ->and($html)->toContain('data-flux-navbar');
});

test('login gate and today keep classic form-post expense create path', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('add-expense'))
        ->assertSuccessful()
        ->assertSee('action="'.route('home.store').'"', false)
        ->assertSee('data-flux-button', false);
});

test('shell exposes today and monthly without hamburger drawer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect($html)->not->toContain('bars-3')
        ->and($html)->not->toContain('Toggle navigation')
        ->and($html)->toContain(__('Account'))
        ->and($html)->toContain(route('home'))
        ->and($html)->toContain(route('dashboard'))
        ->and($html)->toContain(route('profile.edit'))
        ->and($html)->not->toContain('native:bottom-nav');
});

test('secondary screens do not mark today or monthly as current', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('add-expense'))->getContent();

    expect($html)->not->toContain('data-current="data-current"')
        ->and($html)->not->toContain('bars-3')
        ->and($html)->toContain(__('Account'));
});
