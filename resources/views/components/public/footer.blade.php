@props([
    'columns' => [],
])

<footer {{ $attributes->merge(['class' => 'bg-gewo-grey-900 text-white py-12']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- Company Info --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <svg width="32" height="32" viewBox="0 0 40 40" class="text-accent" aria-hidden="true">
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
                        <rect x="24" y="16" width="12" height="6" rx="3" fill="#c8102e"/>
                    </svg>
                    <span class="text-accent font-bold">gewo</span>
                </div>
                <p class="text-gewo-grey-400 text-sm">
                    {{ __('pages.layout.footer.company.name') }}<br>
                    {!! nl2br(e(__('pages.layout.footer.company.address'))) !!}
                </p>
            </div>

            {{-- Dynamic Columns --}}
            @foreach($columns as $column)
                <div>
                    <h4 class="font-semibold mb-4">{{ $column['title'] }}</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        @foreach($column['links'] as $link)
                            <li>
                                <a href="{{ $link['href'] ?? '#' }}" class="hover:text-white transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            {{-- Default columns if none provided --}}
            @if(count($columns) === 0)
                <div>
                    <h4 class="font-semibold mb-4">{{ __('pages.layout.footer.nav.title') }}</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        @foreach(__('pages.layout.footer.nav.links') as $key => $label)
                            <li><a href="#" class="hover:text-white transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">{{ __('pages.layout.footer.service.title') }}</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        @foreach(__('pages.layout.footer.service.links') as $key => $label)
                            <li><a href="#" class="hover:text-white transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">{{ __('pages.layout.footer.legal.title') }}</h4>
                    <ul class="space-y-2 text-sm text-gewo-grey-400">
                        @foreach(__('pages.layout.footer.legal.links') as $key => $label)
                            <li><a href="#" class="hover:text-white transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="border-t border-gewo-grey-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gewo-grey-400">
                {{ __('pages.layout.footer.copyright', ['year' => date('Y')]) }}
            </p>
            <div class="flex items-center gap-2 text-sm text-gewo-grey-400">
                <span>{{ __('pages.layout.footer.font_size') }}</span>
                <button
                    type="button"
                    onclick="document.documentElement.setAttribute('data-font-size', 'small')"
                    class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-xs"
                    aria-label="{{ __('pages.layout.footer.font_size_small') }}"
                >A</button>
                <button
                    type="button"
                    onclick="document.documentElement.setAttribute('data-font-size', 'normal')"
                    class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-sm"
                    aria-label="{{ __('pages.layout.footer.font_size_normal') }}"
                >A</button>
                <button
                    type="button"
                    onclick="document.documentElement.setAttribute('data-font-size', 'large')"
                    class="px-2 py-1 hover:bg-gewo-grey-800 rounded text-base"
                    aria-label="{{ __('pages.layout.footer.font_size_large') }}"
                >A</button>
            </div>
        </div>
    </div>
</footer>
