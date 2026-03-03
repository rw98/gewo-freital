@props(['block', 'preview' => false])

@php
    $headers = $block->getContent('headers', []);
    $rows = $block->getContent('rows', []);
@endphp

@if (empty($headers) && $preview)
    <div class="text-zinc-400 italic text-center py-8">{{ __('pages.blocks.table.placeholder') }}</div>
@else
    <flux:table>
        <flux:table.columns>
            @foreach ($headers as $header)
                <flux:table.column>{{ $header }}</flux:table.column>
            @endforeach
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($rows as $row)
                <flux:table.row>
                    @foreach ($row as $cell)
                        <flux:table.cell>{{ $cell }}</flux:table.cell>
                    @endforeach
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
@endif
