@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.hero.heading') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.heading"
            :placeholder="__('pages.blocks.hero.heading_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.hero.subheading') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.subheading"
            :placeholder="__('pages.blocks.hero.subheading_placeholder')"
            rows="2"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.hero.image') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.image"
            :placeholder="__('pages.blocks.image.url_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.hero.image_description') }}</flux:description>
    </flux:field>

    <flux:separator />

    <flux:heading size="sm">{{ __('pages.blocks.hero.cta') }}</flux:heading>

    <flux:field>
        <flux:label>{{ __('pages.blocks.hero.cta_text') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.cta_text"
            :placeholder="__('pages.blocks.hero.cta_text_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.hero.cta_url') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.cta_url"
            placeholder="https://"
        />
    </flux:field>
</div>
