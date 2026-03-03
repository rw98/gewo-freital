@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.card.title') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.title"
            :placeholder="__('pages.blocks.card.title_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.card.content') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.content"
            :placeholder="__('pages.blocks.card.content_placeholder')"
            rows="4"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.card.image') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.image"
            :placeholder="__('pages.blocks.image.url_placeholder')"
        />
    </flux:field>
</div>
