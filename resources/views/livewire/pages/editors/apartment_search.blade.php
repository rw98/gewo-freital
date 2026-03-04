@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.apartment_search.title') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.title"
            :placeholder="__('pages.blocks.apartment_search.title_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.apartment_search.title_description') }}</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.apartment_search.description') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.description"
            :placeholder="__('pages.blocks.apartment_search.description_placeholder')"
            rows="2"
        />
    </flux:field>

    <flux:separator />

    <flux:field>
        <flux:checkbox
            wire:model.live="editingContent.show_featured"
            label="{{ __('pages.blocks.apartment_search.show_featured') }}"
        />
        <flux:description>{{ __('pages.blocks.apartment_search.show_featured_description') }}</flux:description>
    </flux:field>

    @if ($this->editingContent['show_featured'] ?? true)
        <flux:field>
            <flux:label>{{ __('pages.blocks.apartment_search.featured_count') }}</flux:label>
            <flux:select wire:model.live="editingContent.featured_count">
                @foreach ([1, 2, 3, 4, 6] as $count)
                    <flux:select.option value="{{ $count }}">{{ $count }} {{ __('pages.blocks.apartment_search.apartments') }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
    @endif
</div>
