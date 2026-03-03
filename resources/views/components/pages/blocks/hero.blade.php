@props(['block', 'preview' => false])

@php
    $heading = $block->getContent('heading', '');
    $subheading = $block->getContent('subheading', '');
    $image = $block->getContent('image', '');
    $ctaText = $block->getContent('cta_text', '');
    $ctaUrl = $block->getContent('cta_url', '#');
@endphp

<section class="relative py-16 md:py-24 overflow-hidden rounded-lg {{ $image ? '' : 'bg-gradient-to-br from-accent/10 to-accent/5' }}">
    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black/40"></div>
    @endif

    <div class="relative z-10 max-w-4xl mx-auto text-center px-4 {{ $image ? 'text-white' : '' }}">
        @if ($heading || $preview)
            <flux:heading size="2xl" level="1" class="mb-4">
                {{ $heading ?: __('pages.blocks.hero.heading_placeholder') }}
            </flux:heading>
        @endif

        @if ($subheading || $preview)
            <flux:text size="lg" class="{{ $image ? 'text-white/80' : 'text-zinc-600 dark:text-zinc-400' }} mb-8 max-w-2xl mx-auto">
                {{ $subheading ?: __('pages.blocks.hero.subheading_placeholder') }}
            </flux:text>
        @endif

        @if ($ctaText || $preview)
            <flux:button variant="primary" :href="$ctaUrl">
                {{ $ctaText ?: __('pages.blocks.hero.cta_placeholder') }}
            </flux:button>
        @endif
    </div>
</section>
