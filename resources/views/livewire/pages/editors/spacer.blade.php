@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.spacer.height') }}</flux:label>
        <flux:select wire:model.live="editingContent.height">
            <flux:select.option value="xs">{{ __('pages.blocks.spacer.heights.xs') }}</flux:select.option>
            <flux:select.option value="sm">{{ __('pages.blocks.spacer.heights.sm') }}</flux:select.option>
            <flux:select.option value="md">{{ __('pages.blocks.spacer.heights.md') }}</flux:select.option>
            <flux:select.option value="lg">{{ __('pages.blocks.spacer.heights.lg') }}</flux:select.option>
            <flux:select.option value="xl">{{ __('pages.blocks.spacer.heights.xl') }}</flux:select.option>
        </flux:select>
    </flux:field>
</div>
