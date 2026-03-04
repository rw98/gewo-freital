<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.stack.gap') }}</flux:label>
        <flux:select wire:model.live="editingContent.gap">
            <flux:select.option value="0">{{ __('pages.builder.none') }}</flux:select.option>
            <flux:select.option value="2">{{ __('pages.builder.extra_small') }}</flux:select.option>
            <flux:select.option value="4">{{ __('pages.builder.small') }}</flux:select.option>
            <flux:select.option value="6">{{ __('pages.builder.medium') }}</flux:select.option>
            <flux:select.option value="8">{{ __('pages.builder.large') }}</flux:select.option>
        </flux:select>
        <flux:description>{{ __('pages.blocks.stack.gap_description') }}</flux:description>
    </flux:field>
</div>
