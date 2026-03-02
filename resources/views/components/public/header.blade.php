@props([
    'navItems' => [],
])

<header {{ $attributes->merge(['class' => 'sticky top-0 z-50 bg-white border-b border-gewo-grey-200']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center">
                <x-public.logo />
            </a>

            {{-- Desktop Navigation --}}
            @if(count($navItems) > 0)
                <nav class="hidden lg:flex items-center gap-1">
                    <flux:navbar>
                        @foreach($navItems as $item)
                            <flux:navbar.item
                                :href="$item['href']"
                                :icon="$item['icon'] ?? null"
                            >
                                {{ $item['label'] }}
                            </flux:navbar.item>
                        @endforeach
                    </flux:navbar>
                </nav>
            @endif

            {{-- Right side: Accessibility + Auth --}}
            <div class="flex items-center gap-4">
                {{-- Font Size Selector (Accessibility) --}}
                <div class="hidden md:block">
                    <livewire:font-size-selector />
                </div>

                {{-- Auth buttons --}}
                @if (Route::has('login'))
                    <div class="flex items-center gap-2">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" size="sm">
                                {{ __('pages.layout.auth.dashboard') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('login') }}" variant="primary" size="sm">
                                {{ __('pages.layout.auth.login') }}
                            </flux:button>
                        @endauth
                    </div>
                @endif

                {{-- Mobile menu button --}}
                @if(count($navItems) > 0)
                    <flux:button variant="ghost" size="sm" class="lg:hidden" x-data x-on:click="$dispatch('open-mobile-menu')">
                        <flux:icon name="bars-3" class="size-5" />
                        <span class="sr-only">{{ __('pages.layout.menu_open') }}</span>
                    </flux:button>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- Mobile Navigation Modal --}}
@if(count($navItems) > 0)
    <flux:modal name="mobile-menu" class="lg:hidden">
        <div class="p-4">
            <flux:navlist>
                @foreach($navItems as $item)
                    <flux:navlist.item
                        :href="$item['href']"
                        :icon="$item['icon'] ?? null"
                    >
                        {{ $item['label'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist>
            <div class="mt-6 pt-6 border-t border-gewo-grey-200">
                <livewire:font-size-selector />
            </div>
        </div>
    </flux:modal>
@endif
