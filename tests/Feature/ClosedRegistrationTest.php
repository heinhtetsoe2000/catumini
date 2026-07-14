<?php

use App\Models\User;

test('registration screen is unavailable', function () {
    $this->get('/register')->assertNotFound();
});

test('registration store is unavailable', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

test('seeded owner can still log in', function () {
    $user = User::factory()->create([
        'email' => 'owner@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});
