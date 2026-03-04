@props(['field', 'inRow' => false])

<div
    wire:key="field-{{ $field->id }}"
    wire:sort:item="{{ $field->id }}"
    class="group p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $selectedFieldId === $field->id ? 'bg-accent/5 ring-2 ring-accent ring-inset' : '' }} {{ $inRow ? 'border border-zinc-200 dark:border-zinc-600 rounded-lg' : '' }}"
>
    <div class="flex items-start gap-4">
        {{-- Drag handle --}}
        <div wire:sort:handle class="cursor-move text-zinc-400 hover:text-zinc-600 mt-1">
            <flux:icon name="bars-3" class="size-5" />
        </div>

        {{-- Field info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <flux:icon :name="$field->type->icon()" class="size-4 text-zinc-500" />
                <flux:text class="font-medium">{{ $field->label }}</flux:text>
                @if ($field->is_required)
                    <flux:badge size="sm" color="red">{{ __('forms.builder.required') }}</flux:badge>
                @endif
            </div>
            <flux:text size="sm" class="text-zinc-500 mt-1">
                {{ $field->type->label() }}
                @if ($field->description)
                    &middot; {{ Str::limit($field->description, 40) }}
                @endif
            </flux:text>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            @if ($inRow)
                <flux:button
                    variant="ghost"
                    size="xs"
                    icon="arrow-up-on-square"
                    title="{{ __('forms.builder.move_out_of_row') }}"
                    wire:click="moveFieldOutOfRow('{{ $field->id }}')"
                />
            @endif
            <flux:button
                variant="ghost"
                size="xs"
                icon="pencil"
                wire:click="selectField('{{ $field->id }}')"
            />
            <flux:button
                variant="ghost"
                size="xs"
                icon="document-duplicate"
                wire:click="duplicateField('{{ $field->id }}')"
            />
            <flux:button
                variant="ghost"
                size="xs"
                icon="trash"
                class="text-red-500 hover:text-red-600"
                wire:click="deleteField('{{ $field->id }}')"
                wire:confirm="{{ __('forms.builder.confirm_delete_field') }}"
            />
        </div>
    </div>
</div>
