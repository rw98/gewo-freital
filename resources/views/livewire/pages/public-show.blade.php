@php
    // Optimized for 15" laptops (~1440px screens)
    $layoutClasses = match ($page->layout->value) {
        'full_width' => 'max-w-6xl',   // 1152px
        'sidebar' => 'max-w-5xl',       // 1024px
        'landing' => 'max-w-5xl',       // 1024px
        default => 'max-w-4xl',         // 896px - good for text-heavy content
    };
@endphp

<div class="min-h-screen">
    {{-- Page content --}}
    <article class="{{ $layoutClasses }} mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        @if ($page->blocks->isEmpty())
            <div class="text-center py-16">
                <flux:heading size="lg">{{ __('pages.public.empty') }}</flux:heading>
            </div>
        @else
            <div class="grid grid-cols-12 gap-4 md:gap-6">
                @foreach ($page->blocks as $block)
                    @php
                        $span = $block->column_span;
                        // Responsive column spans: full width on mobile, actual span on larger screens
                        $colClasses = match (true) {
                            $span <= 4 => 'col-span-12 sm:col-span-6 md:col-span-' . $span,
                            $span <= 6 => 'col-span-12 sm:col-span-' . $span,
                            $span <= 8 => 'col-span-12 md:col-span-' . $span,
                            default => 'col-span-12',
                        };
                    @endphp
                    <div wire:key="block-{{ $block->id }}" class="{{ $colClasses }}">
                        <x-pages.block-renderer :block="$block" :preview="false" />
                    </div>
                @endforeach
            </div>
        @endif
    </article>
</div>
