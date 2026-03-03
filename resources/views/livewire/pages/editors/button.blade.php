@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.button.text') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.text"
            :placeholder="__('pages.blocks.button.text_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.button.url') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.url"
            placeholder="https://"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.button.variant') }}</flux:label>
        <flux:select wire:model.live="editingContent.variant">
            <flux:select.option value="primary">{{ __('pages.blocks.button.variants.primary') }}</flux:select.option>
            <flux:select.option value="filled">{{ __('pages.blocks.button.variants.filled') }}</flux:select.option>
            <flux:select.option value="ghost">{{ __('pages.blocks.button.variants.ghost') }}</flux:select.option>
            <flux:select.option value="danger">{{ __('pages.blocks.button.variants.danger') }}</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.button.size') }}</flux:label>
        <flux:select wire:model.live="editingContent.size">
            <flux:select.option value="sm">{{ __('pages.builder.small') }}</flux:select.option>
            <flux:select.option value="base">{{ __('pages.builder.medium') }}</flux:select.option>
        </flux:select>
    </flux:field>
</div>
