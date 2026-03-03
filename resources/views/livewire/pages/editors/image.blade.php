@props(['block'])

<div class="space-y-4">
    {{-- Image preview --}}
    @if (!empty($this->editingContent['src']))
        <div class="relative rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-700">
            <img
                src="{{ $this->editingContent['src'] }}"
                alt="{{ $this->editingContent['alt'] ?? '' }}"
                class="w-full h-auto max-h-48 object-contain"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            />
            <div class="hidden flex-col items-center justify-center h-32 text-zinc-400">
                <flux:icon name="exclamation-triangle" class="size-6" />
                <span class="text-sm mt-1">{{ __('pages.blocks.image.invalid_url') }}</span>
            </div>
        </div>
    @endif

    <flux:field>
        <flux:label>{{ __('pages.blocks.image.url') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.src"
            type="url"
            :placeholder="__('pages.blocks.image.url_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.image.url_description') }}</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.image.alt') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.alt"
            :placeholder="__('pages.blocks.image.alt_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.image.alt_description') }}</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.image.caption') }}</flux:label>
        <flux:input
            wire:model.live.debounce.500ms="editingContent.caption"
            :placeholder="__('pages.blocks.image.caption_placeholder')"
        />
    </flux:field>
</div>
