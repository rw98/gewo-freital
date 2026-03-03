@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.divider.style') }}</flux:label>
        <flux:select wire:model.live="editingContent.style">
            <flux:select.option value="solid">{{ __('pages.blocks.divider.styles.solid') }}</flux:select.option>
            <flux:select.option value="dashed">{{ __('pages.blocks.divider.styles.dashed') }}</flux:select.option>
            <flux:select.option value="dotted">{{ __('pages.blocks.divider.styles.dotted') }}</flux:select.option>
        </flux:select>
    </flux:field>
</div>
