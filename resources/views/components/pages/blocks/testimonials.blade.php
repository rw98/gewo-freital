@props(['block', 'preview' => false])

@php
    $testimonials = $block->getContent('testimonials', []);
@endphp

@if (empty($testimonials) && $preview)
    <div class="grid md:grid-cols-2 gap-6">
        @for ($i = 0; $i < 2; $i++)
            <flux:card class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0"></div>
                    <div>
                        <flux:icon name="chat-bubble-left-right" class="size-8 text-accent/30 mb-2" />
                        <flux:text class="italic text-zinc-600 dark:text-zinc-400 mb-4">
                            "{{ __('pages.blocks.testimonials.quote_placeholder') }}"
                        </flux:text>
                        <flux:heading size="sm">{{ __('pages.blocks.testimonials.name_placeholder') }}</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">{{ __('pages.blocks.testimonials.role_placeholder') }}</flux:text>
                    </div>
                </div>
            </flux:card>
        @endfor
    </div>
@else
    <div class="grid md:grid-cols-2 gap-6">
        @foreach ($testimonials as $testimonial)
            <flux:card class="p-6">
                <div class="flex items-start gap-4">
                    @if (!empty($testimonial['avatar']))
                        <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] ?? '' }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0" />
                    @else
                        <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 flex items-center justify-center">
                            <flux:icon name="user" class="size-6 text-zinc-400" />
                        </div>
                    @endif
                    <div>
                        <flux:icon name="chat-bubble-left-right" class="size-8 text-accent/30 mb-2" />
                        <flux:text class="italic text-zinc-600 dark:text-zinc-400 mb-4">
                            "{{ $testimonial['quote'] ?? '' }}"
                        </flux:text>
                        <flux:heading size="sm">{{ $testimonial['name'] ?? '' }}</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">{{ $testimonial['role'] ?? '' }}</flux:text>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
@endif
