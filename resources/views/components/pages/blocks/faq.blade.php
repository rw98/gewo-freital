@props(['block', 'preview' => false])

@php
    $items = $block->getContent('items', []);
@endphp

@if (empty($items) && $preview)
    <flux:accordion>
        @for ($i = 0; $i < 3; $i++)
            <flux:accordion.item>
                <flux:accordion.heading>{{ __('pages.blocks.faq.question_placeholder') }} {{ $i + 1 }}?</flux:accordion.heading>
                <flux:accordion.content>{{ __('pages.blocks.faq.answer_placeholder') }}</flux:accordion.content>
            </flux:accordion.item>
        @endfor
    </flux:accordion>
@else
    <flux:accordion>
        @foreach ($items as $index => $item)
            <flux:accordion.item :key="$index">
                <flux:accordion.heading>{{ $item['question'] ?? '' }}</flux:accordion.heading>
                <flux:accordion.content>{{ $item['answer'] ?? '' }}</flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
@endif
