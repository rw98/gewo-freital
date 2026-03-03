@props([
    'kwh' => null,
    'class' => null,
    'showValue' => true,
    'showScale' => true,
    'size' => 'default',
])

@php
    use App\Enums\EnergyEfficiencyClass;

    // Determine efficiency class
    if ($class instanceof EnergyEfficiencyClass) {
        $efficiencyClass = $class;
    } elseif (is_string($class)) {
        $efficiencyClass = EnergyEfficiencyClass::tryFrom($class);
    } elseif ($kwh !== null) {
        $efficiencyClass = EnergyEfficiencyClass::fromKwh((float) $kwh);
    } else {
        $efficiencyClass = null;
    }

    $sizes = [
        'sm' => ['scale' => 'h-6', 'arrow' => 'h-4', 'text' => 'text-xs', 'badge' => 'text-sm px-2 py-0.5'],
        'default' => ['scale' => 'h-8', 'arrow' => 'h-5', 'text' => 'text-sm', 'badge' => 'text-base px-3 py-1'],
        'lg' => ['scale' => 'h-10', 'arrow' => 'h-6', 'text' => 'text-base', 'badge' => 'text-lg px-4 py-1.5'],
    ];
    $s = $sizes[$size] ?? $sizes['default'];

    $classes = EnergyEfficiencyClass::cases();
@endphp

@if($efficiencyClass)
    <div {{ $attributes->merge(['class' => 'energy-label']) }}>
        @if($showScale)
            {{-- Energy Scale Bar --}}
            <div class="relative mb-2">
                {{-- Color bars --}}
                <div class="flex rounded-sm overflow-hidden {{ $s['scale'] }}">
                    @foreach($classes as $ec)
                        <div
                            class="flex-1 flex items-center justify-center {{ $s['text'] }} font-bold text-white transition-opacity duration-200 {{ $efficiencyClass === $ec ? 'ring-2 ring-offset-1 ring-gray-800' : 'opacity-70' }}"
                            style="background-color: {{ $ec->hexColor() }};"
                            title="{{ $ec->value }}: {{ $ec->range()['min'] }} - {{ $ec->range()['max'] ?? '>' . $ec->range()['min'] }} kWh/m²a"
                        >
                            {{ $ec->value }}
                        </div>
                    @endforeach
                </div>

                {{-- Arrow indicator --}}
                @php
                    $position = $efficiencyClass->scalePosition();
                    // Calculate position based on which class (each class is ~11.11% of the bar)
                    $classIndex = array_search($efficiencyClass, $classes);
                    $positionPercent = ($classIndex / count($classes) * 100) + (100 / count($classes) / 2);
                @endphp
                <div
                    class="absolute -bottom-2 transform -translate-x-1/2 transition-all duration-300"
                    style="left: {{ $positionPercent }}%;"
                >
                    <svg class="{{ $s['arrow'] }} drop-shadow-md" viewBox="0 0 24 12" fill="currentColor">
                        <path d="M12 0L24 12H0L12 0Z" style="fill: {{ $efficiencyClass->hexColor() }};"/>
                    </svg>
                </div>
            </div>
        @endif

        {{-- Value display --}}
        <div class="flex items-center gap-3 mt-4">
            {{-- Class badge --}}
            <span
                class="inline-flex items-center justify-center font-bold text-white rounded {{ $s['badge'] }} min-w-[3rem]"
                style="background-color: {{ $efficiencyClass->hexColor() }};"
            >
                {{ $efficiencyClass->value }}
            </span>

            @if($showValue && $kwh !== null)
                <span class="{{ $s['text'] }} text-gewo-grey-600">
                    {{ number_format((float) $kwh, 0, ',', '.') }} kWh/m²a
                </span>
            @endif
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'energy-label']) }}>
        <div class="text-gewo-grey-400 {{ $s['text'] }}">
            {{ __('No energy data available') }}
        </div>
    </div>
@endif
