@props(['block', 'preview' => false])

@php
    $tabs = $block->getContent('tabs', []);
@endphp

@if (empty($tabs) && $preview)
    <div class="text-zinc-400 italic text-center py-8">{{ __('pages.blocks.tabs.placeholder') }}</div>
@else
    <flux:tabs>
        @foreach ($tabs as $index => $tab)
            <flux:tab :name="'tab-'.$index">{{ $tab['title'] ?? __('pages.blocks.tabs.tab', ['num' => $index + 1]) }}</flux:tab>
        @endforeach

        @foreach ($tabs as $index => $tab)
            <flux:tab.panel :name="'tab-'.$index" class="py-4">
                {{ $tab['content'] ?? '' }}
            </flux:tab.panel>
        @endforeach
    </flux:tabs>
@endif
