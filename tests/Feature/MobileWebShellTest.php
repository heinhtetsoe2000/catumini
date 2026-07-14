<?php

use App\Models\User;

test('today page does not render native edge components', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('native:top-bar', false);
    $response->assertDontSee('native:bottom-nav', false);
    $response->assertSee(route('add-expense'), false);
    $response->assertSee(__('Today'), false);
});
