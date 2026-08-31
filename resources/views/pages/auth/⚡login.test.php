<?php

use Livewire\Livewire;

test('login page can be rendered', function () {
    Livewire::test('pages::auth.login')
        ->assertStatus(200);
});

test('component exists on the page', function () {
    $this->get('/login')
        ->assertSeeLivewire('pages::auth.login');
});

test('Login page contains email password and remember me fields.', function () {
    Livewire::test('pages::auth.login')
        ->assertsee('Email')
        ->assertSee('Password')
        ->assertSee('Remember me');
});

test('Login page contains forget password link.', function () {
    Livewire::test('pages::auth.login')
        ->assertsee('Forgot your password?');
});

test('Login page contains login button.', function () {
        Livewire::test('pages::auth.login')
        ->assertsee('Log in');
});
