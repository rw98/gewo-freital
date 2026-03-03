@props(['block', 'preview' => false])

@php
    $url = $block->getContent('url', '');
    $provider = $block->getContent('provider', 'youtube');

    // Extract video ID from URL
    $videoId = '';
    if ($provider === 'youtube' && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } elseif ($provider === 'vimeo' && preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
@endphp

@if (empty($url) && $preview)
    <div class="flex flex-col items-center justify-center h-40 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
        <flux:icon name="play-circle" class="size-8 text-zinc-400" />
        <span class="text-zinc-400 text-sm mt-2">{{ __('pages.blocks.video.placeholder') }}</span>
    </div>
@elseif ($videoId)
    <div class="aspect-video rounded-lg overflow-hidden">
        @if ($provider === 'youtube')
            <iframe
                src="https://www.youtube.com/embed/{{ $videoId }}"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
        @elseif ($provider === 'vimeo')
            <iframe
                src="https://player.vimeo.com/video/{{ $videoId }}"
                class="w-full h-full"
                frameborder="0"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen
            ></iframe>
        @endif
    </div>
@endif
