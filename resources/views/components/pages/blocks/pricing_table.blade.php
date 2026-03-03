@props(['block', 'preview' => false])

@php
    $plans = $block->getContent('plans', []);
@endphp

@if (empty($plans) && $preview)
    <div class="grid md:grid-cols-3 gap-6">
        @foreach (['Basic', 'Pro', 'Enterprise'] as $index => $planName)
            <flux:card class="p-6 text-center {{ $index === 1 ? 'ring-2 ring-accent' : '' }}">
                @if ($index === 1)
                    <flux:badge color="accent" class="mb-4">{{ __('pages.blocks.pricing_table.popular') }}</flux:badge>
                @endif
                <flux:heading size="lg" class="mb-2">{{ $planName }}</flux:heading>
                <div class="my-4">
                    <span class="text-4xl font-bold">{{ ($index + 1) * 9 }}</span>
                    <span class="text-zinc-500">/{{ __('pages.blocks.pricing_table.month') }}</span>
                </div>
                <ul class="space-y-2 mb-6 text-left">
                    @for ($i = 0; $i < 3; $i++)
                        <li class="flex items-center gap-2">
                            <flux:icon name="check" class="size-4 text-green-500" />
                            <span>{{ __('pages.blocks.pricing_table.feature_placeholder') }}</span>
                        </li>
                    @endfor
                </ul>
                <flux:button variant="{{ $index === 1 ? 'primary' : 'ghost' }}" class="w-full">
                    {{ __('pages.blocks.pricing_table.select') }}
                </flux:button>
            </flux:card>
        @endforeach
    </div>
@else
    <div class="grid md:grid-cols-{{ count($plans) }} gap-6">
        @foreach ($plans as $plan)
            <flux:card class="p-6 text-center {{ $plan['featured'] ?? false ? 'ring-2 ring-accent' : '' }}">
                @if ($plan['featured'] ?? false)
                    <flux:badge color="accent" class="mb-4">{{ __('pages.blocks.pricing_table.popular') }}</flux:badge>
                @endif
                <flux:heading size="lg" class="mb-2">{{ $plan['name'] ?? '' }}</flux:heading>
                <div class="my-4">
                    <span class="text-4xl font-bold">{{ $plan['price'] ?? 0 }}</span>
                    <span class="text-zinc-500">/{{ $plan['period'] ?? __('pages.blocks.pricing_table.month') }}</span>
                </div>
                @if (!empty($plan['features']))
                    <ul class="space-y-2 mb-6 text-left">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-center gap-2">
                                <flux:icon name="check" class="size-4 text-green-500" />
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <flux:button variant="{{ $plan['featured'] ?? false ? 'primary' : 'ghost' }}" class="w-full" :href="$plan['url'] ?? '#'">
                    {{ $plan['button_text'] ?? __('pages.blocks.pricing_table.select') }}
                </flux:button>
            </flux:card>
        @endforeach
    </div>
@endif
