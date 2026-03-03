@props(['block', 'preview' => false])

@php
    $heading = $block->getContent('heading', '');
    $content = $block->getContent('content', '');
    $buttonText = $block->getContent('button_text', '');
    $buttonUrl = $block->getContent('button_url', '#');
@endphp

<section class="py-12 px-8 rounded-lg bg-gradient-to-r from-accent to-accent/80 text-white text-center">
    @if ($heading || $preview)
        <flux:heading size="xl" level="2" class="mb-4 text-white">
            {{ $heading ?: __('pages.blocks.cta.heading_placeholder') }}
        </flux:heading>
    @endif

    @if ($content || $preview)
        <flux:text size="lg" class="text-white/80 mb-8 max-w-2xl mx-auto">
            {{ $content ?: __('pages.blocks.cta.content_placeholder') }}
        </flux:text>
    @endif

    @if ($buttonText || $preview)
        <flux:button variant="filled" :href="$buttonUrl" class="bg-white text-accent hover:bg-white/90">
            {{ $buttonText ?: __('pages.blocks.cta.button_placeholder') }}
        </flux:button>
    @endif
</section>
