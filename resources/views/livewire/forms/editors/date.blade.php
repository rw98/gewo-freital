<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.date_options') }}</flux:heading>

    <flux:input
        type="date"
        wire:model.live.debounce.500ms="editingField.config.min_date"
        :label="__('forms.builder.min_date')"
    />

    <flux:input
        type="date"
        wire:model.live.debounce.500ms="editingField.config.max_date"
        :label="__('forms.builder.max_date')"
    />
</div>
