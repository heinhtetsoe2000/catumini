<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('password can be updated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('password', 'password')
        ->set('new_password', 'New-Password1!')
        ->set('new_password_confirmation', 'New-Password1!')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('New-Password1!', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::user.profile')
        ->set('password', 'wrong-password')
        ->set('new_password', 'New-Password1!')
        ->set('new_password_confirmation', 'New-Password1!')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});
