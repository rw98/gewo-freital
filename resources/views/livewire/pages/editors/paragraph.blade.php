@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.paragraph.text') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.text"
            :placeholder="__('pages.blocks.paragraph.text_placeholder')"
            rows="6"
        />
    </flux:field>
</div>
