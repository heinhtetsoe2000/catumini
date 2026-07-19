<?php

use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('save')
        ->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('password', 'password')
        ->call('deleteAccount')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('password', 'wrong-password')
        ->call('deleteAccount')
        ->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('unverified user sees email verification callout on profile', function () {
    $user = User::factory()->unverified()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->assertSee(__('Email Verification'))
        ->assertSee(__('Verify Email'));
});

test('profile page shows log out control with wire click', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->assertSee(__('Log Out'))
        ->assertSeeHtml('wire:click="logout"');
});

test('user can log out from profile', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->call('logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
