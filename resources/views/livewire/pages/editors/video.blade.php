@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.video.url') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.url"
            :placeholder="__('pages.blocks.video.url_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.video.url_description') }}</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.video.provider') }}</flux:label>
        <flux:select wire:model.live="editingContent.provider">
            <flux:select.option value="youtube">YouTube</flux:select.option>
            <flux:select.option value="vimeo">Vimeo</flux:select.option>
        </flux:select>
    </flux:field>
</div>
