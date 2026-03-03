@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.heading.text') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.text"
            :placeholder="__('pages.blocks.heading.text_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.heading.level') }}</flux:label>
        <flux:select wire:model.live="editingContent.level">
            <flux:select.option value="1">H1</flux:select.option>
            <flux:select.option value="2">H2</flux:select.option>
            <flux:select.option value="3">H3</flux:select.option>
            <flux:select.option value="4">H4</flux:select.option>
            <flux:select.option value="5">H5</flux:select.option>
            <flux:select.option value="6">H6</flux:select.option>
        </flux:select>
    </flux:field>
</div>
