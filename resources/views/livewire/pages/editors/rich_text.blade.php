@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.rich_text.content') }}</flux:label>
        <flux:editor
            wire:model.live.debounce.500ms="editingContent.html"
            toolbar="heading | bold italic strike | bullet ordered blockquote | link | align"
        />
    </flux:field>
</div>
