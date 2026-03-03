@props(['block', 'preview' => false])

@php
    $items = $block->getContent('items', []);
@endphp

@if (empty($items) && $preview)
    <div class="text-zinc-400 italic text-center py-8">{{ __('pages.blocks.accordion.placeholder') }}</div>
@else
    <flux:accordion>
        @foreach ($items as $index => $item)
            <flux:accordion.item :key="$index">
                <flux:accordion.heading>{{ $item['title'] ?? '' }}</flux:accordion.heading>
                <flux:accordion.content>{{ $item['content'] ?? '' }}</flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
@endif
