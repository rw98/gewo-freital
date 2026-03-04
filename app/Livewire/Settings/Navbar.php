<?php

namespace App\Livewire\Settings;

use App\Enums\PageStatus;
use App\Models\GlobalSetting;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Navbar-Einstellungen')]
class Navbar extends Component
{
    use AuthorizesRequests;

    /** @var array<int, array{type: string, page_id?: string, url?: string, label: string, icon?: string, children?: array}> */
    public array $items = [];

    public bool $showNavbar = true;

    public bool $showIconPicker = false;

    public ?int $iconPickerItemIndex = null;

    public ?int $iconPickerChildIndex = null;

    public string $iconSearch = '';

    /**
     * Common Heroicons for navigation.
     *
     * @var array<string>
     */
    public const AVAILABLE_ICONS = [
        // Navigation & UI
        'home', 'home-modern', 'building-office', 'building-office-2', 'building-storefront',
        'bars-3', 'bars-4', 'squares-2x2', 'rectangle-group', 'queue-list',
        'magnifying-glass', 'adjustments-horizontal', 'adjustments-vertical',
        'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down',
        'chevron-right', 'chevron-left', 'chevron-up', 'chevron-down',
        'arrow-top-right-on-square', 'arrow-up-right', 'arrows-pointing-out',

        // Actions
        'plus', 'minus', 'x-mark', 'check', 'pencil', 'trash',
        'document', 'document-text', 'document-duplicate', 'folder', 'folder-open',
        'clipboard', 'clipboard-document', 'clipboard-document-list',
        'archive-box', 'inbox', 'paper-airplane', 'envelope', 'envelope-open',

        // Communication
        'chat-bubble-left', 'chat-bubble-left-right', 'chat-bubble-oval-left',
        'phone', 'phone-arrow-up-right', 'device-phone-mobile',
        'at-symbol', 'hashtag', 'megaphone', 'speaker-wave',

        // People & Users
        'user', 'user-circle', 'user-group', 'users', 'user-plus',
        'identification', 'finger-print', 'hand-raised',

        // Commerce & Finance
        'shopping-cart', 'shopping-bag', 'credit-card', 'banknotes', 'currency-euro',
        'receipt-percent', 'calculator', 'wallet', 'gift',

        // Content & Media
        'photo', 'camera', 'video-camera', 'film', 'musical-note',
        'book-open', 'newspaper', 'bookmark', 'tag', 'hashtag',

        // Location & Travel
        'map', 'map-pin', 'globe-alt', 'globe-europe-africa',
        'truck', 'rocket-launch', 'paper-airplane',

        // Time & Calendar
        'calendar', 'calendar-days', 'clock', 'bell', 'bell-alert',

        // Status & Info
        'information-circle', 'question-mark-circle', 'exclamation-circle', 'exclamation-triangle',
        'check-circle', 'x-circle', 'no-symbol',
        'shield-check', 'shield-exclamation', 'lock-closed', 'lock-open', 'key',

        // Settings & Tools
        'cog-6-tooth', 'cog-8-tooth', 'wrench', 'wrench-screwdriver',
        'adjustments-horizontal', 'funnel', 'swatch',

        // Misc
        'star', 'heart', 'fire', 'bolt', 'sparkles', 'sun', 'moon',
        'eye', 'eye-slash', 'link', 'cursor-arrow-rays',
        'cloud', 'server', 'cpu-chip', 'wifi', 'signal',
        'chart-bar', 'chart-pie', 'presentation-chart-line',
        'lifebuoy', 'light-bulb', 'puzzle-piece', 'flag', 'trophy',
    ];

    public function mount(): void
    {
        $this->authorize('manage-pages');

        $settings = GlobalSetting::getNavbarSettings();
        $this->items = $settings['items'] ?? [];
        $this->showNavbar = $settings['show_navbar'] ?? true;
    }

    /**
     * @return Collection<int, Page>
     */
    #[Computed]
    public function availablePages(): Collection
    {
        return Page::query()
            ->where('status', PageStatus::Published)
            ->whereNotNull('published_at')
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);
    }

    public function addPageLink(): void
    {
        $this->items[] = [
            'type' => 'page',
            'page_id' => '',
            'label' => '',
            'icon' => '',
        ];
        $this->save();
    }

    public function addExternalLink(): void
    {
        $this->items[] = [
            'type' => 'external',
            'url' => '',
            'label' => '',
            'icon' => '',
        ];
        $this->save();
    }

    public function addDropdown(): void
    {
        $this->items[] = [
            'type' => 'dropdown',
            'label' => '',
            'icon' => '',
            'children' => [],
        ];
        $this->save();
    }

    public function addChildPageLink(int $parentIndex): void
    {
        if (! isset($this->items[$parentIndex]['children'])) {
            $this->items[$parentIndex]['children'] = [];
        }

        $this->items[$parentIndex]['children'][] = [
            'type' => 'page',
            'page_id' => '',
            'label' => '',
            'icon' => '',
        ];
        $this->save();
    }

    public function addChildExternalLink(int $parentIndex): void
    {
        if (! isset($this->items[$parentIndex]['children'])) {
            $this->items[$parentIndex]['children'] = [];
        }

        $this->items[$parentIndex]['children'][] = [
            'type' => 'external',
            'url' => '',
            'label' => '',
            'icon' => '',
        ];
        $this->save();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->save();
    }

    public function removeChildItem(int $parentIndex, int $childIndex): void
    {
        if (isset($this->items[$parentIndex]['children'][$childIndex])) {
            unset($this->items[$parentIndex]['children'][$childIndex]);
            $this->items[$parentIndex]['children'] = array_values($this->items[$parentIndex]['children']);
            $this->save();
        }
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $items = $this->items;
        $temp = $items[$index - 1];
        $items[$index - 1] = $items[$index];
        $items[$index] = $temp;
        $this->items = $items;
        $this->save();
    }

    public function moveDown(int $index): void
    {
        if ($index >= count($this->items) - 1) {
            return;
        }

        $items = $this->items;
        $temp = $items[$index + 1];
        $items[$index + 1] = $items[$index];
        $items[$index] = $temp;
        $this->items = $items;
        $this->save();
    }

    public function moveChildUp(int $parentIndex, int $childIndex): void
    {
        if ($childIndex <= 0 || ! isset($this->items[$parentIndex]['children'])) {
            return;
        }

        $children = $this->items[$parentIndex]['children'];
        $temp = $children[$childIndex - 1];
        $children[$childIndex - 1] = $children[$childIndex];
        $children[$childIndex] = $temp;
        $this->items[$parentIndex]['children'] = $children;
        $this->save();
    }

    public function moveChildDown(int $parentIndex, int $childIndex): void
    {
        if (! isset($this->items[$parentIndex]['children'])) {
            return;
        }

        $children = $this->items[$parentIndex]['children'];
        if ($childIndex >= count($children) - 1) {
            return;
        }

        $temp = $children[$childIndex + 1];
        $children[$childIndex + 1] = $children[$childIndex];
        $children[$childIndex] = $temp;
        $this->items[$parentIndex]['children'] = $children;
        $this->save();
    }

    public function updateItem(int $index, string $field, mixed $value): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index][$field] = $value;
            $this->save();
        }
    }

    public function updateChildItem(int $parentIndex, int $childIndex, string $field, mixed $value): void
    {
        if (isset($this->items[$parentIndex]['children'][$childIndex])) {
            $this->items[$parentIndex]['children'][$childIndex][$field] = $value;
            $this->save();
        }
    }

    public function updatedShowNavbar(): void
    {
        $this->save();
    }

    public function openIconPicker(int $itemIndex, ?int $childIndex = null): void
    {
        $this->iconPickerItemIndex = $itemIndex;
        $this->iconPickerChildIndex = $childIndex;
        $this->iconSearch = '';
        $this->showIconPicker = true;
    }

    public function selectIcon(string $icon): void
    {
        if ($this->iconPickerItemIndex === null) {
            return;
        }

        if ($this->iconPickerChildIndex !== null) {
            $this->updateChildItem($this->iconPickerItemIndex, $this->iconPickerChildIndex, 'icon', $icon);
        } else {
            $this->updateItem($this->iconPickerItemIndex, 'icon', $icon);
        }

        $this->closeIconPicker();
    }

    public function clearIcon(): void
    {
        if ($this->iconPickerItemIndex === null) {
            return;
        }

        if ($this->iconPickerChildIndex !== null) {
            $this->updateChildItem($this->iconPickerItemIndex, $this->iconPickerChildIndex, 'icon', '');
        } else {
            $this->updateItem($this->iconPickerItemIndex, 'icon', '');
        }

        $this->closeIconPicker();
    }

    public function closeIconPicker(): void
    {
        $this->showIconPicker = false;
        $this->iconPickerItemIndex = null;
        $this->iconPickerChildIndex = null;
        $this->iconSearch = '';
    }

    /**
     * @return array<string>
     */
    #[Computed]
    public function filteredIcons(): array
    {
        if (empty($this->iconSearch)) {
            return self::AVAILABLE_ICONS;
        }

        $search = strtolower($this->iconSearch);

        return array_values(array_filter(self::AVAILABLE_ICONS, function ($icon) use ($search) {
            return str_contains(strtolower($icon), $search);
        }));
    }

    public function save(): void
    {
        GlobalSetting::setValue('navbar', [
            'show_navbar' => $this->showNavbar,
            'items' => $this->items,
        ]);

        $this->dispatch('navbar-settings-saved');
    }

    public function render(): View
    {
        return view('livewire.settings.navbar');
    }
}
