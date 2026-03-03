@props(['block', 'preview' => false])

@php
    $items = $block->getContent('items', []);
    $style = $block->getContent('style', 'bullet');
@endphp

@if (empty($items) && $preview)
    <div class="text-zinc-400 italic">{{ __('pages.blocks.list.placeholder') }}</div>
@else
    @if ($style === 'numbered')
        <ol class="list-decimal list-inside space-y-1">
            @foreach ($items as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    @else
        <ul class="list-disc list-inside space-y-1">
            @foreach ($items as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @endif
@endif
