<?php

use App\Enums\Appearance;
use App\Enums\DisplayLanguage;
use App\Models\Expense;
use App\Models\User;
use App\Support\ExpenseDayLabel;
use App\Support\OwnerDisplayPreferences;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

it('shows burmese copy on the login gate when the locale cookie is my', function () {
    $response = $this->withCookie(OwnerDisplayPreferences::localeCookieName(), DisplayLanguage::My->value)
        ->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('Your personal expense ledger.', locale: 'my'), false);
    $response->assertSee(__('Login', locale: 'my'), false);
});

it('sets the locale cookie from accept language on first visit', function () {
    $response = $this->withHeader('Accept-Language', 'my-MM,my;q=0.9,en;q=0.8')
        ->get('/');

    $response->assertSuccessful();
    $response->assertCookie(OwnerDisplayPreferences::localeCookieName(), DisplayLanguage::My->value);
});

it('persists an authenticated owner language toggle and reloads the ui', function () {
    $user = User::factory()->create([
        'display_language' => DisplayLanguage::En,
    ]);

    $this->actingAs($user)
        ->post(route('preferences.display-language'), [
            'display_language' => DisplayLanguage::My->value,
        ])
        ->assertRedirect();

    expect($user->fresh()->display_language)->toBe(DisplayLanguage::My);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertSuccessful()
        ->assertSee(__('Display language', locale: 'my'), false);
});

it('localizes today and yesterday day group headers for my locale', function () {
    $user = User::factory()->create([
        'display_language' => DisplayLanguage::My,
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => now()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 2000,
        'spent_on' => now()->subDay()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 3000,
        'spent_on' => now()->subDays(2)->toDateString(),
    ]);

    App::setLocale('my');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee(__('Today', locale: 'my'), false)
        ->assertSee(__('Yesterday', locale: 'my'), false)
        ->assertSee(
            ExpenseDayLabel::for(Carbon\Carbon::parse(now()->subDays(2)->toDateString(), 'Asia/Yangon')),
            false
        );
});

it('renders validation errors in burmese when locale is my', function () {
    $user = User::factory()->create([
        'display_language' => DisplayLanguage::My,
    ]);

    App::setLocale('my');

    $component = Livewire::actingAs($user)
        ->test('pages::home')
        ->set('name', '')
        ->set('amount', 1000)
        ->call('save')
        ->assertHasErrors(['name']);

    expect($component->instance()->getErrorBag()->first('name'))
        ->toBe('အမည် အကွက်ကို ဖြည့်ရန်လိုအပ်ပါသည်။');
});

it('persists dark appearance from profile and renders the dark shell', function () {
    $user = User::factory()->create([
        'appearance' => Appearance::System,
    ]);

    $this->actingAs($user)
        ->post(route('preferences.appearance'), [
            'appearance' => Appearance::Dark->value,
        ])
        ->assertRedirect();

    expect($user->fresh()->appearance)->toBe(Appearance::Dark);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertSuccessful()
        ->assertSee("window.Flux?.applyAppearance('dark')", false);
});

it('treats system appearance as the default resolved mode', function () {
    $user = User::factory()->create([
        'appearance' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertSuccessful()
        ->assertSee("window.Flux?.applyAppearance('system')", false);
});

it('applies a guest appearance cookie on the login gate without a toggle', function () {
    $response = $this->withCookie(OwnerDisplayPreferences::appearanceCookieName(), Appearance::Dark->value)
        ->get('/');

    $response->assertSuccessful();
    $response->assertSee("window.Flux?.applyAppearance('dark')", false);
});

it('persists browser detected language for an owner on first authenticated request', function () {
    $user = User::factory()->create([
        'display_language' => null,
    ]);

    $this->withCookie(OwnerDisplayPreferences::localeCookieName(), DisplayLanguage::My->value)
        ->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful();

    expect($user->fresh()->display_language)->toBe(DisplayLanguage::My);
});
