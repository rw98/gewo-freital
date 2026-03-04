<?php

namespace App\Models;

use App\Livewire\Settings\Navbar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GlobalSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Get a global setting value.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("global_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    /**
     * Set a global setting value.
     *
     * @param  mixed  $value
     */
    public static function setValue(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("global_setting_{$key}");
    }

    /**
     * Get the global navbar settings.
     *
     * @return array{show_navbar: bool, items: array<int, array{type: string, page_id?: string, url?: string, label: string, icon?: string}>}
     */
    public static function getNavbarSettings(): array
    {
        return self::getValue('navbar', [
            'show_navbar' => true,
            'items' => [],
        ]);
    }

    /**
     * Get the resolved navbar items for display.
     *
     * @return array<int, array{href?: string, label: string, icon?: string, children?: array}>
     */
    public static function getNavbarItems(): array
    {
        $settings = self::getNavbarSettings();

        if (! ($settings['show_navbar'] ?? true)) {
            return [];
        }

        $items = $settings['items'] ?? [];

        return collect($items)->map(function ($item) {
            return self::resolveNavItem($item);
        })->filter()->values()->toArray();
    }

    /**
     * Resolve a single nav item (page, external, or dropdown).
     *
     * @return array{href?: string, label: string, icon?: string, children?: array}|null
     */
    private static function resolveNavItem(array $item): ?array
    {
        $type = $item['type'] ?? 'page';
        $validatedIcon = self::validateIcon($item['icon'] ?? null);

        // Handle dropdown with children
        if ($type === 'dropdown') {
            $children = collect($item['children'] ?? [])->map(function ($child) {
                return self::resolveNavItem($child);
            })->filter()->values()->toArray();

            // Only include dropdown if it has children or a label
            if (empty($children) && empty($item['label'])) {
                return null;
            }

            return [
                'label' => $item['label'] ?? '',
                'icon' => $validatedIcon,
                'children' => $children,
            ];
        }

        // Handle page reference
        if ($type === 'page' && isset($item['page_id']) && $item['page_id']) {
            $page = Page::find($item['page_id']);
            if ($page && $page->isPublished()) {
                return [
                    'href' => route('pages.show', $page->slug),
                    'label' => $item['label'] ?: $page->title,
                    'icon' => $validatedIcon,
                ];
            }

            return null;
        }

        // Handle external URL
        if ($type === 'external' && ! empty($item['url'])) {
            return [
                'href' => $item['url'],
                'label' => $item['label'] ?? '',
                'icon' => $validatedIcon,
            ];
        }

        return null;
    }

    /**
     * Validate an icon name against the available icons list.
     */
    private static function validateIcon(?string $icon): ?string
    {
        if (empty($icon)) {
            return null;
        }

        return in_array($icon, Navbar::AVAILABLE_ICONS, true) ? $icon : null;
    }

    /**
     * Get the branding settings.
     *
     * @return array{logo_url: string|null, favicon_url: string|null, site_name: string|null}
     */
    public static function getBrandingSettings(): array
    {
        return self::getValue('branding', [
            'logo_url' => null,
            'favicon_url' => null,
            'site_name' => null,
        ]);
    }

    /**
     * Get the custom logo URL or null for default.
     */
    public static function getLogoUrl(): ?string
    {
        $settings = self::getBrandingSettings();

        return $settings['logo_url'] ?? null;
    }

    /**
     * Get the custom favicon URL or null for default.
     */
    public static function getFaviconUrl(): ?string
    {
        $settings = self::getBrandingSettings();

        return $settings['favicon_url'] ?? null;
    }

    /**
     * Get the custom site name or config default.
     */
    public static function getSiteName(): string
    {
        $settings = self::getBrandingSettings();

        return $settings['site_name'] ?? config('app.name');
    }
}
