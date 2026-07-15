<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user create command creates a login-ready user with email and password', function () {
    $this->artisan('user:create', [
        '--email' => 'owner@example.com',
        '--password' => 'password',
    ])->assertSuccessful()
        ->expectsOutputToContain('User [owner@example.com] created successfully.');

    $user = User::query()->where('email', 'owner@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeTrue();

    $this->post('/login', [
        'email' => 'owner@example.com',
        'password' => 'password',
    ])->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('user create command applies an optional name', function () {
    $this->artisan('user:create', [
        '--email' => 'named@example.com',
        '--password' => 'password',
        '--name' => 'Ledger Owner',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'email' => 'named@example.com',
        'name' => 'Ledger Owner',
    ]);
});

test('user create command defaults name from email local part when omitted', function () {
    $this->artisan('user:create', [
        '--email' => 'heinhtet@example.com',
        '--password' => 'password',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'email' => 'heinhtet@example.com',
        'name' => 'heinhtet',
    ]);
});

test('user create command rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->artisan('user:create', [
        '--email' => 'taken@example.com',
        '--password' => 'password',
    ])->assertFailed()
        ->expectsOutputToContain('email');

    expect(User::query()->where('email', 'taken@example.com')->count())->toBe(1);
});

test('user create command rejects missing email', function () {
    $this->artisan('user:create', [
        '--password' => 'password',
    ])->assertFailed();

    expect(User::query()->count())->toBe(0);
});

test('user create command rejects missing password', function () {
    $this->artisan('user:create', [
        '--email' => 'owner@example.com',
    ])->assertFailed();

    $this->assertDatabaseMissing('users', ['email' => 'owner@example.com']);
});
