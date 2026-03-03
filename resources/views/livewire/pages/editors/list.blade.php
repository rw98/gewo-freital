@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.list.style') }}</flux:label>
        <flux:select wire:model.live="editingContent.style">
            <flux:select.option value="bullet">{{ __('pages.blocks.list.styles.bullet') }}</flux:select.option>
            <flux:select.option value="numbered">{{ __('pages.blocks.list.styles.numbered') }}</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.list.items') }}</flux:label>
        <flux:description>{{ __('pages.blocks.list.items_description') }}</flux:description>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.items_text"
            :placeholder="__('pages.blocks.list.items_placeholder')"
            rows="6"
        />
    </flux:field>
</div>
