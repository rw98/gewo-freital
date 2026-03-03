@props(['block', 'preview' => false])

@php
    $buttons = $block->getContent('buttons', []);
@endphp

@if (empty($buttons) && $preview)
    <div class="flex gap-2">
        <flux:button variant="primary" disabled>{{ __('pages.blocks.button_group.button') }} 1</flux:button>
        <flux:button variant="ghost" disabled>{{ __('pages.blocks.button_group.button') }} 2</flux:button>
    </div>
@else
    <div class="flex flex-wrap gap-2">
        @foreach ($buttons as $button)
            <flux:button
                :variant="$button['variant'] ?? 'primary'"
                :size="$button['size'] ?? 'base'"
                :href="$button['url'] ?? '#'"
            >
                {{ $button['text'] ?? '' }}
            </flux:button>
        @endforeach
    </div>
@endif
