<?php

use App\Models\User;

it('shows the login gate for guests', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(config('app.name'), false);
    $response->assertSee('Your personal expense ledger.', false);
    $response->assertSee(route('login'), false);
    $response->assertSee('Login', false);
    $response->assertDontSee('Register', false);
    $response->assertDontSee('nativephp.com', false);
    $response->assertDontSee('discord.gg/nativephp', false);
    $response->assertDontSee('bifrost.nativephp.com', false);
    $response->assertDontSee('viewBox="0 0 316 316"', false);
});

it('redirects authenticated users from the login gate to today', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect(route('home'));
});

it('shows wordmark branding on login without the laravel logo', function () {
    $response = $this->get(route('login'));

    $response->assertSuccessful();

    $html = $response->getContent();

    expect($html)
        ->toContain((string) config('app.name'))
        ->and($html)->not->toContain('viewBox="0 0 316 316"')
        ->and(strlen($html))->toBeGreaterThan(100);
});
