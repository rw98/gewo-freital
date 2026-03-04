<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.checkbox_options') }}</flux:heading>

    <flux:checkbox
        wire:model.live="editingField.config.default_checked"
        :label="__('forms.builder.default_checked')"
    />
</div>
