@props(['block'])

<div class="space-y-4">
    <flux:callout icon="information-circle">
        {{ __('pages.blocks.table.editor_info') }}
    </flux:callout>

    <flux:field>
        <flux:label>{{ __('pages.blocks.table.headers') }}</flux:label>
        <flux:description>{{ __('pages.blocks.table.headers_description') }}</flux:description>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.headers_text"
            :placeholder="__('pages.blocks.table.headers_placeholder')"
        />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.table.rows') }}</flux:label>
        <flux:description>{{ __('pages.blocks.table.rows_description') }}</flux:description>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.rows_text"
            :placeholder="__('pages.blocks.table.rows_placeholder')"
            rows="6"
        />
    </flux:field>
</div>
