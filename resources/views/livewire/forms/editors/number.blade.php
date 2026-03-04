<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.number_options') }}</flux:heading>

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.min"
        :label="__('forms.builder.min_value')"
    />

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.max"
        :label="__('forms.builder.max_value')"
    />

    <flux:input
        type="number"
        wire:model.live.debounce.500ms="editingField.config.step"
        :label="__('forms.builder.step')"
        min="0.01"
        step="0.01"
    />
</div>
