<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.file_options') }}</flux:heading>

    <flux:input
        wire:model.live.debounce.500ms="editingField.config.allowed_extensions"
        :label="__('forms.builder.allowed_extensions')"
        :description="__('forms.builder.allowed_extensions_description')"
        :placeholder="__('forms.builder.allowed_extensions_placeholder')"
    />

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.max_size_kb"
        :label="__('forms.builder.max_size_kb')"
        :description="__('forms.builder.max_size_description')"
        min="1"
    />
</div>
