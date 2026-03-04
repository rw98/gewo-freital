@props(['sourceTypes', 'sourceFields'])

<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
    <flux:heading size="sm">{{ __('forms.builder.autofill_options') }}</flux:heading>

    <flux:select
        wire:model.live="editingField.config.autofill_source"
        :label="__('forms.builder.autofill_source')"
    >
        <flux:select.option value="">{{ __('forms.builder.autofill_none') }}</flux:select.option>
        @foreach ($sourceTypes as $value => $label)
            <flux:select.option :value="$value">{{ $label }}</flux:select.option>
        @endforeach
    </flux:select>

    @if (!empty($editingField['config']['autofill_source'] ?? ''))
        @php
            $currentSource = $editingField['config']['autofill_source'];
            $fields = $sourceFields[$currentSource] ?? [];
        @endphp

        @if (!empty($fields))
            <flux:select
                wire:model.live="editingField.config.autofill_field"
                :label="__('forms.builder.autofill_field')"
            >
                <flux:select.option value="">{{ __('forms.builder.autofill_select_field') }}</flux:select.option>
                @foreach ($fields as $fieldKey => $fieldMeta)
                    <flux:select.option :value="$fieldKey">{{ $fieldMeta['label'] }}</flux:select.option>
                @endforeach
            </flux:select>
        @endif
    @endif

    <flux:description>{{ __('forms.builder.autofill_description') }}</flux:description>
</div>
