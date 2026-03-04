<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.textarea_options') }}</flux:heading>

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.rows"
        :label="__('forms.builder.rows')"
        min="2"
        max="20"
    />

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.max_length"
        :label="__('forms.builder.max_length')"
        min="1"
    />
</div>
