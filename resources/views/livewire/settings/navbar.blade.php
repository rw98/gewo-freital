<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('pages.navbar.heading')" :subheading="__('pages.navbar.subheading')">
        <div class="my-6 space-y-6">
            {{-- Show/Hide Navbar Toggle --}}
            <flux:field>
                <flux:checkbox wire:model.live="showNavbar" label="{{ __('pages.navbar.show_navbar') }}" />
                <flux:description>{{ __('pages.navbar.show_navbar_description') }}</flux:description>
            </flux:field>

            @if ($showNavbar)
                <flux:separator />

                {{-- Navigation Items --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">{{ __('pages.navbar.items') }}</flux:heading>
                        <div class="flex gap-2">
                            <flux:button variant="ghost" size="sm" icon="document" wire:click="addPageLink">
                                {{ __('pages.navbar.add_page') }}
                            </flux:button>
                            <flux:button variant="ghost" size="sm" icon="link" wire:click="addExternalLink">
                                {{ __('pages.navbar.add_link') }}
                            </flux:button>
                            <flux:button variant="ghost" size="sm" icon="chevron-down" wire:click="addDropdown">
                                {{ __('pages.navbar.add_dropdown') }}
                            </flux:button>
                        </div>
                    </div>

                    @if (count($items) === 0)
                        <div class="text-center py-8 text-zinc-500 dark:text-zinc-400 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <flux:icon name="bars-3" class="size-8 mx-auto mb-2 opacity-50" />
                            <flux:text>{{ __('pages.navbar.empty') }}</flux:text>
                            <flux:text size="sm" class="mt-2">{{ __('pages.navbar.empty_hint') }}</flux:text>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($items as $index => $item)
                                <div wire:key="nav-item-{{ $index }}" class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                    <div class="flex items-start gap-3">
                                        {{-- Reorder buttons --}}
                                        <div class="flex flex-col gap-1 pt-1">
                                            <button wire:click="moveUp({{ $index }})" class="p-1 text-zinc-400 hover:text-zinc-600 disabled:opacity-30" @disabled($index === 0)>
                                                <flux:icon name="chevron-up" class="size-4" />
                                            </button>
                                            <button wire:click="moveDown({{ $index }})" class="p-1 text-zinc-400 hover:text-zinc-600 disabled:opacity-30" @disabled($index === count($items) - 1)>
                                                <flux:icon name="chevron-down" class="size-4" />
                                            </button>
                                        </div>

                                        {{-- Item fields --}}
                                        <div class="flex-1 space-y-3">
                                            <div class="flex items-center gap-2 mb-2">
                                                <flux:badge size="sm" :color="match($item['type'] ?? 'page') { 'page' => 'blue', 'external' => 'amber', 'dropdown' => 'purple', default => 'zinc' }">
                                                    {{ __('pages.navbar.type_' . ($item['type'] ?? 'page')) }}
                                                </flux:badge>
                                            </div>

                                            @if (($item['type'] ?? 'page') === 'page')
                                                {{-- Page selector --}}
                                                <flux:field>
                                                    <flux:label>{{ __('pages.navbar.select_page') }}</flux:label>
                                                    <flux:select wire:change="updateItem({{ $index }}, 'page_id', $event.target.value)">
                                                        <flux:select.option value="" :selected="empty($item['page_id'])">{{ __('pages.navbar.select_page_placeholder') }}</flux:select.option>
                                                        @foreach ($this->availablePages as $availablePage)
                                                            <flux:select.option value="{{ $availablePage->id }}" :selected="($item['page_id'] ?? '') === $availablePage->id">{{ $availablePage->title }} (/p/{{ $availablePage->slug }})</flux:select.option>
                                                        @endforeach
                                                    </flux:select>
                                                </flux:field>
                                            @elseif (($item['type'] ?? 'page') === 'external')
                                                {{-- External URL --}}
                                                <flux:field>
                                                    <flux:label>{{ __('pages.navbar.url') }}</flux:label>
                                                    <flux:input
                                                        value="{{ $item['url'] ?? '' }}"
                                                        placeholder="https://"
                                                        wire:blur="updateItem({{ $index }}, 'url', $event.target.value)"
                                                    />
                                                </flux:field>
                                            @endif

                                            <div class="grid grid-cols-2 gap-3">
                                                {{-- Label --}}
                                                <flux:field>
                                                    <flux:label>{{ __('pages.navbar.label') }}</flux:label>
                                                    <flux:input
                                                        value="{{ $item['label'] ?? '' }}"
                                                        placeholder="{{ __('pages.navbar.label_placeholder') }}"
                                                        wire:blur="updateItem({{ $index }}, 'label', $event.target.value)"
                                                    />
                                                    <flux:description>{{ __('pages.navbar.label_description') }}</flux:description>
                                                </flux:field>

                                                {{-- Icon Picker --}}
                                                <flux:field>
                                                    <flux:label>{{ __('pages.navbar.icon') }}</flux:label>
                                                    <button
                                                        type="button"
                                                        wire:click="openIconPicker({{ $index }})"
                                                        class="flex items-center gap-2 w-full px-3 py-2 text-left text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg hover:border-zinc-300 dark:hover:border-zinc-500 bg-white dark:bg-zinc-800"
                                                    >
                                                        @if ($item['icon'] ?? null)
                                                            <flux:icon :name="$item['icon']" class="size-5 text-zinc-600 dark:text-zinc-400" />
                                                            <span class="text-zinc-700 dark:text-zinc-300">{{ $item['icon'] }}</span>
                                                        @else
                                                            <flux:icon name="squares-plus" class="size-5 text-zinc-400" />
                                                            <span class="text-zinc-400">{{ __('pages.navbar.select_icon') }}</span>
                                                        @endif
                                                    </button>
                                                </flux:field>
                                            </div>

                                            {{-- Dropdown children --}}
                                            @if (($item['type'] ?? 'page') === 'dropdown')
                                                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-600">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <flux:text size="sm" class="font-medium">{{ __('pages.navbar.dropdown_items') }}</flux:text>
                                                        <div class="flex gap-2">
                                                            <flux:button variant="ghost" size="xs" icon="document" wire:click="addChildPageLink({{ $index }})">
                                                                {{ __('pages.navbar.add_page') }}
                                                            </flux:button>
                                                            <flux:button variant="ghost" size="xs" icon="link" wire:click="addChildExternalLink({{ $index }})">
                                                                {{ __('pages.navbar.add_link') }}
                                                            </flux:button>
                                                        </div>
                                                    </div>

                                                    @if (empty($item['children']))
                                                        <div class="text-center py-4 text-zinc-400 border border-dashed border-zinc-300 dark:border-zinc-600 rounded">
                                                            <flux:text size="sm">{{ __('pages.navbar.dropdown_empty') }}</flux:text>
                                                        </div>
                                                    @else
                                                        <div class="space-y-2 pl-4 border-l-2 border-zinc-200 dark:border-zinc-600">
                                                            @foreach ($item['children'] as $childIndex => $child)
                                                                <div wire:key="nav-child-{{ $index }}-{{ $childIndex }}" class="flex items-start gap-2 p-3 bg-white dark:bg-zinc-700 rounded border border-zinc-200 dark:border-zinc-600">
                                                                    {{-- Reorder --}}
                                                                    <div class="flex flex-col gap-0.5">
                                                                        <button wire:click="moveChildUp({{ $index }}, {{ $childIndex }})" class="p-0.5 text-zinc-400 hover:text-zinc-600 disabled:opacity-30" @disabled($childIndex === 0)>
                                                                            <flux:icon name="chevron-up" class="size-3" />
                                                                        </button>
                                                                        <button wire:click="moveChildDown({{ $index }}, {{ $childIndex }})" class="p-0.5 text-zinc-400 hover:text-zinc-600 disabled:opacity-30" @disabled($childIndex === count($item['children']) - 1)>
                                                                            <flux:icon name="chevron-down" class="size-3" />
                                                                        </button>
                                                                    </div>

                                                                    <div class="flex-1 space-y-2">
                                                                        <flux:badge size="sm" :color="($child['type'] ?? 'page') === 'page' ? 'blue' : 'amber'">
                                                                            {{ __('pages.navbar.type_' . ($child['type'] ?? 'page')) }}
                                                                        </flux:badge>

                                                                        @if (($child['type'] ?? 'page') === 'page')
                                                                            <flux:select wire:change="updateChildItem({{ $index }}, {{ $childIndex }}, 'page_id', $event.target.value)" size="sm">
                                                                                <flux:select.option value="" :selected="empty($child['page_id'])">{{ __('pages.navbar.select_page_placeholder') }}</flux:select.option>
                                                                                @foreach ($this->availablePages as $availablePage)
                                                                                    <flux:select.option value="{{ $availablePage->id }}" :selected="($child['page_id'] ?? '') === $availablePage->id">{{ $availablePage->title }}</flux:select.option>
                                                                                @endforeach
                                                                            </flux:select>
                                                                        @else
                                                                            <flux:input
                                                                                value="{{ $child['url'] ?? '' }}"
                                                                                placeholder="https://"
                                                                                size="sm"
                                                                                wire:blur="updateChildItem({{ $index }}, {{ $childIndex }}, 'url', $event.target.value)"
                                                                            />
                                                                        @endif

                                                                        <div class="grid grid-cols-2 gap-2">
                                                                            <flux:input
                                                                                value="{{ $child['label'] ?? '' }}"
                                                                                placeholder="{{ __('pages.navbar.label_placeholder') }}"
                                                                                size="sm"
                                                                                wire:blur="updateChildItem({{ $index }}, {{ $childIndex }}, 'label', $event.target.value)"
                                                                            />
                                                                            <button
                                                                                type="button"
                                                                                wire:click="openIconPicker({{ $index }}, {{ $childIndex }})"
                                                                                class="flex items-center gap-1 px-2 py-1.5 text-left text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg hover:border-zinc-300 dark:hover:border-zinc-500 bg-white dark:bg-zinc-800"
                                                                            >
                                                                                @if ($child['icon'] ?? null)
                                                                                    <flux:icon :name="$child['icon']" class="size-4 text-zinc-600 dark:text-zinc-400" />
                                                                                    <span class="text-xs text-zinc-700 dark:text-zinc-300 truncate">{{ $child['icon'] }}</span>
                                                                                @else
                                                                                    <flux:icon name="squares-plus" class="size-4 text-zinc-400" />
                                                                                    <span class="text-xs text-zinc-400">{{ __('pages.navbar.icon') }}</span>
                                                                                @endif
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <button wire:click="removeChildItem({{ $index }}, {{ $childIndex }})" wire:confirm="{{ __('pages.navbar.confirm_remove') }}" class="p-1 text-zinc-400 hover:text-red-500">
                                                                        <flux:icon name="trash" class="size-4" />
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Delete button --}}
                                        <button wire:click="removeItem({{ $index }})" wire:confirm="{{ __('pages.navbar.confirm_remove') }}" class="p-2 text-zinc-400 hover:text-red-500">
                                            <flux:icon name="trash" class="size-5" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Preview --}}
                    @if (count($items) > 0)
                        <flux:separator class="my-6" />

                        <div>
                            <flux:heading size="sm" class="mb-3">{{ __('pages.navbar.preview') }}</flux:heading>
                            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center gap-6">
                                    @foreach ($items as $item)
                                        @php
                                            $label = $item['label'] ?? '';
                                            if (!$label && ($item['type'] ?? 'page') === 'page' && ($item['page_id'] ?? '')) {
                                                $page = $this->availablePages->firstWhere('id', $item['page_id']);
                                                $label = $page?->title ?? __('pages.navbar.untitled');
                                            }
                                            $isDropdown = ($item['type'] ?? 'page') === 'dropdown';
                                            $iconName = $item['icon'] ?? null;
                                            $hasValidIcon = $iconName && in_array($iconName, \App\Livewire\Settings\Navbar::AVAILABLE_ICONS);
                                        @endphp
                                        <div class="relative group">
                                            <div class="flex items-center gap-1 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer">
                                                @if ($hasValidIcon)
                                                    <flux:icon :name="$iconName" class="size-4" />
                                                @endif
                                                <span>{{ $label ?: __('pages.navbar.untitled') }}</span>
                                                @if ($isDropdown)
                                                    <flux:icon name="chevron-down" class="size-3" />
                                                @endif
                                            </div>
                                            @if ($isDropdown && !empty($item['children']))
                                                <div class="absolute left-0 top-full mt-1 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded shadow-lg py-1 min-w-[150px] hidden group-hover:block z-10">
                                                    @foreach ($item['children'] as $child)
                                                        @php
                                                            $childLabel = $child['label'] ?? '';
                                                            if (!$childLabel && ($child['type'] ?? 'page') === 'page' && ($child['page_id'] ?? '')) {
                                                                $childPage = $this->availablePages->firstWhere('id', $child['page_id']);
                                                                $childLabel = $childPage?->title ?? __('pages.navbar.untitled');
                                                            }
                                                            $childIconName = $child['icon'] ?? null;
                                                            $hasValidChildIcon = $childIconName && in_array($childIconName, \App\Livewire\Settings\Navbar::AVAILABLE_ICONS);
                                                        @endphp
                                                        <div class="flex items-center gap-2 px-3 py-1.5 text-sm text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-600">
                                                            @if ($hasValidChildIcon)
                                                                <flux:icon :name="$childIconName" class="size-3" />
                                                            @endif
                                                            <span>{{ $childLabel ?: __('pages.navbar.untitled') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <x-action-message on="navbar-settings-saved">
                {{ __('pages.navbar.saved') }}
            </x-action-message>
        </div>
    </x-settings.layout>

    {{-- Icon Picker Modal --}}
    <flux:modal wire:model="showIconPicker" class="max-w-2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">{{ __('pages.navbar.select_icon') }}</flux:heading>
                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="closeIconPicker" />
            </div>

            {{-- Search --}}
            <flux:input
                wire:model.live.debounce.200ms="iconSearch"
                icon="magnifying-glass"
                placeholder="{{ __('pages.navbar.search_icons') }}"
                class="mb-4"
            />

            {{-- Clear icon button --}}
            <div class="mb-4">
                <flux:button variant="ghost" size="sm" wire:click="clearIcon" icon="x-mark">
                    {{ __('pages.navbar.no_icon') }}
                </flux:button>
            </div>

            {{-- Icon Grid --}}
            <div class="grid grid-cols-8 gap-2 max-h-[400px] overflow-y-auto">
                @foreach ($this->filteredIcons as $icon)
                    <button
                        type="button"
                        wire:click="selectIcon('{{ $icon }}')"
                        class="flex flex-col items-center justify-center p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors"
                        title="{{ $icon }}"
                    >
                        <flux:icon :name="$icon" class="size-6 text-zinc-600 dark:text-zinc-400" />
                    </button>
                @endforeach
            </div>

            @if (count($this->filteredIcons) === 0)
                <div class="text-center py-8 text-zinc-500">
                    <flux:icon name="magnifying-glass" class="size-8 mx-auto mb-2 opacity-50" />
                    <flux:text>{{ __('pages.navbar.no_icons_found') }}</flux:text>
                </div>
            @endif
        </div>
    </flux:modal>
</section>
