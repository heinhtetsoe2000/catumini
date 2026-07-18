<?php

use App\Models\User;
use Livewire\Livewire;

test('today page does not render native edge components', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('native:top-bar', false);
    $response->assertDontSee('native:bottom-nav', false);
    $response->assertSee('Add Expense', false);
    $response->assertSee(__('Today'), false);
    $response->assertSee(__('Home'), false);
    $response->assertSee(__('History'), false);
    $response->assertSee(__('Profile'), false);
    $response->assertSee(config('app.name'), false);
    $response->assertDontSee('viewBox="0 0 316 316"', false);
    $response->assertSee('data-flux-navbar', false);
});

test('add expense is only via the Today modal CTA not a top-nav peer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect(preg_match_all('#href="[^"]*add-expense[^"]*"#', $html))->toBe(0)
        ->and($html)->toContain('Add Expense')
        ->and($html)->toContain('data-modal="add-expense"')
        ->and($html)->toContain('data-flux-navbar');
});

test('today creates expenses through the livewire home page', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSee('Add Expense')
        ->assertSeeHtml('wire:submit="save"');
});

test('shell exposes home and history without hamburger drawer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect($html)->not->toContain('bars-3')
        ->and($html)->not->toContain('Toggle navigation')
        ->and($html)->toContain(__('Home'))
        ->and($html)->toContain(__('History'))
        ->and($html)->toContain(__('Profile'))
        ->and($html)->toContain(route('home'))
        ->and($html)->toContain(route('dashboard'))
        ->and($html)->toContain(route('profile'))
        ->and($html)->not->toContain('native:bottom-nav');
});

test('profile shell keeps home and history available without a hamburger drawer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('profile'))->getContent();

    expect($html)->not->toContain('bars-3')
        ->and($html)->not->toContain('Toggle navigation')
        ->and($html)->toContain(__('Profile'))
        ->and($html)->toContain(route('home'))
        ->and($html)->toContain(route('dashboard'));
});
