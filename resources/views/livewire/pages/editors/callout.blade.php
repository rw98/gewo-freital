@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.callout.type') }}</flux:label>
        <flux:select wire:model.live="editingContent.type">
            <flux:select.option value="info">{{ __('pages.blocks.callout.types.info') }}</flux:select.option>
            <flux:select.option value="success">{{ __('pages.blocks.callout.types.success') }}</flux:select.option>
            <flux:select.option value="warning">{{ __('pages.blocks.callout.types.warning') }}</flux:select.option>
            <flux:select.option value="danger">{{ __('pages.blocks.callout.types.danger') }}</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.callout.title') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.title"
            :placeholder="__('pages.blocks.callout.title_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.callout.content') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.content"
            :placeholder="__('pages.blocks.callout.content_placeholder')"
            rows="3"
        />
    </flux:field>
</div>
