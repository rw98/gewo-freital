<?php

use App\Enums\PageEditorRole;
use App\Enums\PageStatus;
use App\Livewire\Settings\Navbar;
use App\Models\GlobalSetting;
use App\Models\Page;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    GlobalSetting::where('key', 'navbar')->delete();
});

it('denies access to users without page role', function () {
    $user = User::factory()->create(['page_role' => null, 'is_admin' => false]);

    $this->actingAs($user)
        ->get(route('settings.navbar'))
        ->assertForbidden();
});

it('allows access to page editors', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    $this->actingAs($user)
        ->get(route('settings.navbar'))
        ->assertOk();
});

it('allows access to page admins', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Admin]);

    $this->actingAs($user)
        ->get(route('settings.navbar'))
        ->assertOk();
});

it('allows access to system admins', function () {
    $user = User::factory()->create(['is_admin' => true, 'page_role' => null]);

    $this->actingAs($user)
        ->get(route('settings.navbar'))
        ->assertOk();
});

it('can add a page link', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('addPageLink')
        ->assertSet('items', [
            [
                'type' => 'page',
                'page_id' => '',
                'label' => '',
                'icon' => '',
            ],
        ]);
});

it('can add an external link', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('addExternalLink')
        ->assertSet('items', [
            [
                'type' => 'external',
                'url' => '',
                'label' => '',
                'icon' => '',
            ],
        ]);
});

it('can remove an item', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'page', 'page_id' => '', 'label' => 'First', 'icon' => ''],
            ['type' => 'page', 'page_id' => '', 'label' => 'Second', 'icon' => ''],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('removeItem', 0)
        ->assertCount('items', 1);
});

it('can reorder items', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'page', 'page_id' => '', 'label' => 'First', 'icon' => ''],
            ['type' => 'page', 'page_id' => '', 'label' => 'Second', 'icon' => ''],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('moveDown', 0)
        ->assertSet('items.0.label', 'Second')
        ->assertSet('items.1.label', 'First');
});

it('saves settings to database', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('addPageLink')
        ->set('items.0.label', 'Test Link')
        ->call('save');

    $settings = GlobalSetting::getNavbarSettings();
    expect($settings['items'][0]['label'])->toBe('Test Link');
});

it('can toggle navbar visibility', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->set('showNavbar', false)
        ->call('save');

    $settings = GlobalSetting::getNavbarSettings();
    expect($settings['show_navbar'])->toBeFalse();
});

it('resolves page references to URLs', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => PageStatus::Published,
        'published_at' => now(),
    ]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'page', 'page_id' => $page->id, 'label' => '', 'icon' => ''],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toHaveCount(1);
    expect($navItems[0]['href'])->toBe(route('pages.show', 'test-page'));
    expect($navItems[0]['label'])->toBe('Test Page');
});

it('uses custom label when provided for page reference', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => PageStatus::Published,
        'published_at' => now(),
    ]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'page', 'page_id' => $page->id, 'label' => 'Custom Label', 'icon' => 'home'],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems[0]['label'])->toBe('Custom Label');
    expect($navItems[0]['icon'])->toBe('home');
});

it('filters out unpublished pages', function () {
    $publishedPage = Page::factory()->create([
        'title' => 'Published Page',
        'slug' => 'published',
        'status' => PageStatus::Published,
        'published_at' => now(),
    ]);

    $draftPage = Page::factory()->create([
        'title' => 'Draft Page',
        'slug' => 'draft',
        'status' => PageStatus::Draft,
        'published_at' => null,
    ]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'page', 'page_id' => $publishedPage->id, 'label' => '', 'icon' => ''],
            ['type' => 'page', 'page_id' => $draftPage->id, 'label' => '', 'icon' => ''],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toHaveCount(1);
    expect($navItems[0]['label'])->toBe('Published Page');
});

it('includes external links', function () {
    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'external', 'url' => 'https://example.com', 'label' => 'External', 'icon' => 'link'],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toHaveCount(1);
    expect($navItems[0]['href'])->toBe('https://example.com');
    expect($navItems[0]['label'])->toBe('External');
});

it('returns empty array when navbar is hidden', function () {
    GlobalSetting::setValue('navbar', [
        'show_navbar' => false,
        'items' => [
            ['type' => 'external', 'url' => 'https://example.com', 'label' => 'External', 'icon' => ''],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toBeEmpty();
});

it('can add a dropdown', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('addDropdown')
        ->assertSet('items.0.type', 'dropdown')
        ->assertSet('items.0.children', []);
});

it('can add child items to dropdown', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'dropdown', 'label' => 'Menu', 'icon' => '', 'children' => []],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('addChildPageLink', 0)
        ->assertCount('items.0.children', 1);
});

it('resolves dropdown with children', function () {
    $page = Page::factory()->create([
        'title' => 'Child Page',
        'slug' => 'child-page',
        'status' => PageStatus::Published,
        'published_at' => now(),
    ]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            [
                'type' => 'dropdown',
                'label' => 'Menu',
                'icon' => 'bars-3',
                'children' => [
                    ['type' => 'page', 'page_id' => $page->id, 'label' => '', 'icon' => ''],
                    ['type' => 'external', 'url' => 'https://example.com', 'label' => 'External', 'icon' => ''],
                ],
            ],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toHaveCount(1);
    expect($navItems[0]['label'])->toBe('Menu');
    expect($navItems[0]['children'])->toHaveCount(2);
    expect($navItems[0]['children'][0]['label'])->toBe('Child Page');
    expect($navItems[0]['children'][1]['label'])->toBe('External');
});

it('can update item fields', function () {
    $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'external', 'url' => '', 'label' => '', 'icon' => ''],
        ],
    ]);

    Livewire::actingAs($user)
        ->test(Navbar::class)
        ->call('updateItem', 0, 'label', 'Test Label')
        ->call('updateItem', 0, 'icon', 'home');

    $settings = GlobalSetting::getNavbarSettings();
    expect($settings['items'][0]['label'])->toBe('Test Label');
    expect($settings['items'][0]['icon'])->toBe('home');
});

it('filters out invalid icons when resolving nav items', function () {
    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            ['type' => 'external', 'url' => 'https://valid.com', 'label' => 'Valid Icon', 'icon' => 'home'],
            ['type' => 'external', 'url' => 'https://invalid.com', 'label' => 'Invalid Icon', 'icon' => 'non-existent-icon-name'],
            ['type' => 'external', 'url' => 'https://empty.com', 'label' => 'No Icon', 'icon' => ''],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems)->toHaveCount(3);
    expect($navItems[0]['icon'])->toBe('home');
    expect($navItems[1]['icon'])->toBeNull();
    expect($navItems[2]['icon'])->toBeNull();
});

it('filters out invalid icons in dropdown children', function () {
    GlobalSetting::setValue('navbar', [
        'show_navbar' => true,
        'items' => [
            [
                'type' => 'dropdown',
                'label' => 'Menu',
                'icon' => 'bars-3',
                'children' => [
                    ['type' => 'external', 'url' => 'https://example.com', 'label' => 'Valid', 'icon' => 'star'],
                    ['type' => 'external', 'url' => 'https://example.com', 'label' => 'Invalid', 'icon' => 'fake-icon'],
                ],
            ],
        ],
    ]);

    $navItems = GlobalSetting::getNavbarItems();

    expect($navItems[0]['icon'])->toBe('bars-3');
    expect($navItems[0]['children'][0]['icon'])->toBe('star');
    expect($navItems[0]['children'][1]['icon'])->toBeNull();
});
