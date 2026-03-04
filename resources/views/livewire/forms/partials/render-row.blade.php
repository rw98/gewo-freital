@props(['row', 'depth' => 0])

@php
    $columns = $row->getConfig('columns', [1, 1, 1]);
    $columnCount = count($columns);
    $totalSpan = array_sum($columns);
@endphp

<div class="grid gap-4" style="grid-template-columns: repeat({{ $totalSpan }}, minmax(0, 1fr));">
    @foreach ($columns as $colIndex => $span)
        @php
            $columnFields = $this->getColumnFields($row->id, $colIndex);
        @endphp
        <div class="col-span-{{ $span }} space-y-6">
            @foreach ($columnFields as $field)
                @if ($field->type->value === 'row')
                    {{-- Nested Row --}}
                    @if ($depth < 2)
                        @include('livewire.forms.partials.render-row', ['row' => $field, 'depth' => $depth + 1])
                    @endif
                @else
                    <x-forms.field-renderer
                        :field="$field"
                        :name="$field->name"
                        :locked="$this->isFieldLocked($field->name)"
                        wire:model="{{ $field->type->value === 'file' ? 'files.'.$field->name : 'values.'.$field->name }}"
                    />
                @endif
            @endforeach
        </div>
    @endforeach
</div>
