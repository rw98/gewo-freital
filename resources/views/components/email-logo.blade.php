@props([
    'size' => 'default',
    'showText' => true,
])

@php
    $sizes = [
        'sm' => ['svg' => '32', 'text' => '18px'],
        'default' => ['svg' => '40', 'text' => '24px'],
        'lg' => ['svg' => '48', 'text' => '28px'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];
    $accentColor = '#064e3b';
    $redAccent = '#c8102e';
@endphp

<div {{ $attributes->merge(['style' => 'display: flex; align-items: center; gap: 12px; text-decoration: none;']) }}>
    {{-- GEWO Logo dots pattern --}}
    <svg viewBox="0 0 40 40" style="width: {{ $s['svg'] }}px; height: {{ $s['svg'] }}px;" aria-hidden="true">
        <circle cx="5" cy="5" r="3" fill="{{ $accentColor }}"/>
        <circle cx="13" cy="5" r="3" fill="{{ $accentColor }}"/>
        <circle cx="21" cy="5" r="3" fill="{{ $accentColor }}"/>
        <circle cx="29" cy="5" r="3" fill="{{ $accentColor }}"/>
        <circle cx="5" cy="13" r="3" fill="{{ $accentColor }}"/>
        <circle cx="13" cy="13" r="3" fill="{{ $accentColor }}"/>
        <circle cx="21" cy="13" r="3" fill="{{ $accentColor }}"/>
        <circle cx="5" cy="21" r="3" fill="{{ $accentColor }}"/>
        <circle cx="13" cy="21" r="3" fill="{{ $accentColor }}"/>
        <circle cx="5" cy="29" r="3" fill="{{ $accentColor }}"/>
        {{-- Red accent --}}
        <rect x="24" y="16" width="12" height="6" rx="3" fill="{{ $redAccent }}"/>
    </svg>
    @if($showText)
        <span style="font-weight: bold; font-size: {{ $s['text'] }}; color: {{ $accentColor }};">gewo</span>
    @endif
</div>
