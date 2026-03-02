@props([
    'size' => 'default',
    'showText' => true,
])

@php
    $sizes = [
        'sm' => ['svg' => 'w-8 h-8', 'text' => 'text-base', 'subtext' => 'text-[10px]'],
        'default' => ['svg' => 'w-10 h-10', 'text' => 'text-lg', 'subtext' => 'text-xs'],
        'lg' => ['svg' => 'w-12 h-12', 'text' => 'text-xl', 'subtext' => 'text-sm'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <div class="flex items-center gap-1">
        {{-- GEWO Logo dots pattern --}}
        <svg viewBox="0 0 40 40" class="{{ $s['svg'] }} text-accent" aria-hidden="true">
            <circle cx="5" cy="5" r="3" fill="currentColor"/>
            <circle cx="13" cy="5" r="3" fill="currentColor"/>
            <circle cx="21" cy="5" r="3" fill="currentColor"/>
            <circle cx="29" cy="5" r="3" fill="currentColor"/>
            <circle cx="5" cy="13" r="3" fill="currentColor"/>
            <circle cx="13" cy="13" r="3" fill="currentColor"/>
            <circle cx="21" cy="13" r="3" fill="currentColor"/>
            <circle cx="5" cy="21" r="3" fill="currentColor"/>
            <circle cx="13" cy="21" r="3" fill="currentColor"/>
            <circle cx="5" cy="29" r="3" fill="currentColor"/>
            {{-- Red accent --}}
            <rect x="24" y="16" width="12" height="6" rx="3" fill="#c8102e"/>
        </svg>
    </div>
    @if($showText)
        <div class="hidden sm:block">
            <span class="text-accent font-bold {{ $s['text'] }}">gewo</span>
            <span class="text-gewo-grey-800 {{ $s['subtext'] }} block leading-tight">{{ __('pages.landing.header.company_name') }}</span>
        </div>
    @endif
</div>
