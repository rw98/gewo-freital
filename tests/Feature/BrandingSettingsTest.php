<?php

use App\Enums\PageEditorRole;
use App\Livewire\Settings\Branding;
use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    GlobalSetting::where('key', 'branding')->delete();
    Storage::fake('public');
});

it('denies access to users without page role', function () {
    $user = User::factory()->create(['page_role' => null, 'is_admin' => false]);

    $this->actingAs($user)
        ->get(route('settings.branding'))
        ->assertForbidden();
});

it('allows access to page editors', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    $this->actingAs($user)
        ->get(route('settings.branding'))
        ->assertOk();
});

it('allows access to system admins', function () {
    $user = User::factory()->create(['is_admin' => true, 'page_role' => null]);

    $this->actingAs($user)
        ->get(route('settings.branding'))
        ->assertOk();
});

it('can update site name', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Branding::class)
        ->set('siteName', 'My Custom Site')
        ->call('save');

    expect(GlobalSetting::getSiteName())->toBe('My Custom Site');
});

it('can upload a logo', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
    $file = UploadedFile::fake()->image('logo.png', 200, 100);

    Livewire::actingAs($user)
        ->test(Branding::class)
        ->set('logoUpload', $file)
        ->assertDispatched('branding-saved');

    expect(GlobalSetting::getLogoUrl())->not->toBeNull();
});

it('can upload a favicon', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
    $file = UploadedFile::fake()->image('favicon.png', 32, 32);

    Livewire::actingAs($user)
        ->test(Branding::class)
        ->set('faviconUpload', $file)
        ->assertDispatched('branding-saved');

    expect(GlobalSetting::getFaviconUrl())->not->toBeNull();
});

it('can remove logo', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('branding', [
        'logo_url' => 'https://example.com/logo.png',
        'favicon_url' => null,
        'site_name' => null,
    ]);

    Livewire::actingAs($user)
        ->test(Branding::class)
        ->call('removeLogo');

    expect(GlobalSetting::getLogoUrl())->toBeNull();
});

it('can remove favicon', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('branding', [
        'logo_url' => null,
        'favicon_url' => 'https://example.com/favicon.ico',
        'site_name' => null,
    ]);

    Livewire::actingAs($user)
        ->test(Branding::class)
        ->call('removeFavicon');

    expect(GlobalSetting::getFaviconUrl())->toBeNull();
});

it('returns default site name when not set', function () {
    expect(GlobalSetting::getSiteName())->toBe(config('app.name'));
});
