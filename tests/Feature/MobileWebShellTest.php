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
    $response->assertSee(config('app.name'), false);
    $response->assertDontSee('viewBox="0 0 316 316"', false);
});

test('add expense is only via the Today CTA not a top-nav peer', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect(preg_match_all('#href="[^"]*add-expense[^"]*"#', $html))->toBe(1)
        ->and($html)->toContain('Add Expense');
});
