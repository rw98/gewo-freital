@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.grid.columns') }}</flux:label>
        <flux:select wire:model.live="editingContent.columns">
            <flux:select.option value="2">2 {{ __('pages.blocks.grid.columns_label') }}</flux:select.option>
            <flux:select.option value="3">3 {{ __('pages.blocks.grid.columns_label') }}</flux:select.option>
            <flux:select.option value="4">4 {{ __('pages.blocks.grid.columns_label') }}</flux:select.option>
            <flux:select.option value="6">6 {{ __('pages.blocks.grid.columns_label') }}</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.grid.gap') }}</flux:label>
        <flux:select wire:model.live="editingContent.gap">
            <flux:select.option value="2">{{ __('pages.builder.small') }}</flux:select.option>
            <flux:select.option value="4">{{ __('pages.builder.medium') }}</flux:select.option>
            <flux:select.option value="6">{{ __('pages.builder.large') }}</flux:select.option>
            <flux:select.option value="8">{{ __('pages.blocks.grid.extra_large') }}</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:callout icon="information-circle" class="mt-4">
        {{ __('pages.blocks.grid.children_info') }}
    </flux:callout>
</div>
