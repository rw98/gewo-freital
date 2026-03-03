@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.cta.heading') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.heading"
            :placeholder="__('pages.blocks.cta.heading_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.cta.content') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.content"
            :placeholder="__('pages.blocks.cta.content_placeholder')"
            rows="2"
        />
    </flux:field>

    <flux:separator />

    <flux:heading size="sm">{{ __('pages.blocks.cta.button') }}</flux:heading>

    <flux:field>
        <flux:label>{{ __('pages.blocks.cta.button_text') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.button_text"
            :placeholder="__('pages.blocks.cta.button_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.cta.button_url') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.button_url"
            placeholder="https://"
        />
    </flux:field>
</div>
