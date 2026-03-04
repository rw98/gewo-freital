<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.info_options') }}</flux:heading>

    <flux:select wire:model.live="editingField.config.style" :label="__('forms.builder.info_style')">
        <flux:select.option value="default">{{ __('forms.builder.info_style_default') }}</flux:select.option>
        <flux:select.option value="info">{{ __('forms.builder.info_style_info') }}</flux:select.option>
        <flux:select.option value="warning">{{ __('forms.builder.info_style_warning') }}</flux:select.option>
        <flux:select.option value="success">{{ __('forms.builder.info_style_success') }}</flux:select.option>
    </flux:select>

    <flux:editor
        wire:model.live="editingField.config.content"
        :label="__('forms.builder.info_content')"
        toolbar="bold italic | bullet ordered | link"
        :placeholder="__('forms.builder.info_content_placeholder')"
    />

    <flux:description>{{ __('forms.builder.info_description') }}</flux:description>
</div>
