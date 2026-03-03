@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.columns.layout') }}</flux:label>
        <flux:select wire:model.live="editingContent.layout">
            <flux:select.option value="1/2-1/2">50% - 50%</flux:select.option>
            <flux:select.option value="1/3-2/3">33% - 66%</flux:select.option>
            <flux:select.option value="2/3-1/3">66% - 33%</flux:select.option>
            <flux:select.option value="1/4-3/4">25% - 75%</flux:select.option>
            <flux:select.option value="3/4-1/4">75% - 25%</flux:select.option>
            <flux:select.option value="1/3-1/3-1/3">33% - 33% - 33%</flux:select.option>
            <flux:select.option value="1/4-1/4-1/4-1/4">25% - 25% - 25% - 25%</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:callout icon="information-circle" class="mt-4">
        {{ __('pages.blocks.columns.children_info') }}
    </flux:callout>
</div>
