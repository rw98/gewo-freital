@props(['row', 'depth' => 0])

@php
    $columns = $row->getConfig('columns', [1, 1, 1]);
    $columnCount = count($columns);
    $totalSpan = array_sum($columns);
@endphp

<div
    wire:key="field-{{ $row->id }}"
    wire:sort:item="{{ $row->id }}"
    class="group p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors {{ $selectedFieldId === $row->id ? 'bg-accent/5 ring-2 ring-accent ring-inset' : '' }}"
>
    {{-- Row Header --}}
    <div class="flex items-start gap-4 mb-4">
        {{-- Drag handle --}}
        <div wire:sort:handle class="cursor-move text-zinc-400 hover:text-zinc-600 mt-1">
            <flux:icon name="bars-3" class="size-5" />
        </div>

        {{-- Row info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <flux:icon name="view-columns" class="size-4 text-zinc-500" />
                <flux:text class="font-medium">{{ __('enums.form_field_type.row') }}</flux:text>
                <flux:badge size="sm" color="zinc">{{ $columnCount }} {{ trans_choice('forms.builder.columns_count', $columnCount, ['default' => 'Spalten']) }}</flux:badge>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <flux:button
                variant="ghost"
                size="xs"
                icon="pencil"
                wire:click="selectField('{{ $row->id }}')"
            />
            <flux:button
                variant="ghost"
                size="xs"
                icon="trash"
                class="text-red-500 hover:text-red-600"
                wire:click="deleteField('{{ $row->id }}')"
                wire:confirm="{{ __('forms.builder.confirm_delete_field') }}"
            />
        </div>
    </div>

    {{-- Row Columns --}}
    <div class="ml-9 grid gap-4" style="grid-template-columns: repeat({{ $totalSpan }}, minmax(0, 1fr));">
        @foreach ($columns as $colIndex => $span)
            @php
                $columnFields = $row->children->where('column_index', $colIndex)->sortBy('order');
            @endphp
            <div
                class="col-span-{{ $span }} min-h-[80px] border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-2 space-y-2"
                x-data
            >
                {{-- Column Header --}}
                <div class="flex items-center justify-between text-xs text-zinc-500 pb-2 border-b border-zinc-200 dark:border-zinc-600">
                    <span>{{ __('forms.builder.row_column', ['number' => $colIndex + 1]) }}</span>
                    <button
                        type="button"
                        class="hover:text-accent"
                        x-on:click="addToRow = '{{ $row->id }}'; addToColumn = {{ $colIndex }}; $wire.set('showFieldPicker', true)"
                    >
                        <flux:icon name="plus" class="size-4" />
                    </button>
                </div>

                {{-- Column Fields --}}
                @forelse ($columnFields as $field)
                    @if ($field->type->value === 'row')
                        {{-- Nested Row --}}
                        @if ($depth < 2)
                            @include('livewire.forms.partials.row-field', ['row' => $field, 'depth' => $depth + 1])
                        @else
                            <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded text-xs text-yellow-700 dark:text-yellow-300">
                                {{ __('forms.builder.max_nesting_reached') }}
                            </div>
                        @endif
                    @else
                        @include('livewire.forms.partials.field-item', ['field' => $field, 'inRow' => true])
                    @endif
                @empty
                    <div class="flex items-center justify-center h-full text-zinc-400 text-sm">
                        {{ __('forms.builder.row_drop_field') }}
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
