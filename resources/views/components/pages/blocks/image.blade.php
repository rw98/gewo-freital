@props(['block', 'preview' => false])

@php
    $src = $block->getContent('src', '');
    $alt = $block->getContent('alt', '');
    $caption = $block->getContent('caption', '');
@endphp

@if ($preview && empty($src))
    {{-- Empty state with inline URL input --}}
    <div
        class="flex flex-col items-center justify-center h-40 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 cursor-pointer hover:border-accent transition-colors"
        x-data="{
            editing: false,
            url: '',
            save() {
                if (this.url.trim()) {
                    $wire.updateInlineText('{{ $block->id }}', 'src', this.url.trim());
                }
                this.editing = false;
            }
        }"
        x-on:click.stop="if (!editing) { editing = true; $nextTick(() => $refs.urlInput.focus()); }"
    >
        <template x-if="!editing">
            <div class="flex flex-col items-center">
                <flux:icon name="photo" class="size-8 text-zinc-400" />
                <span class="text-zinc-400 text-sm mt-2">{{ __('pages.blocks.image.click_to_add') }}</span>
            </div>
        </template>

        <template x-if="editing">
            <div class="w-full px-4" x-on:click.stop>
                <flux:input
                    x-ref="urlInput"
                    x-model="url"
                    type="url"
                    placeholder="{{ __('pages.blocks.image.url_placeholder') }}"
                    x-on:keydown.enter.prevent="save()"
                    x-on:keydown.escape.prevent="editing = false"
                    x-on:blur="save()"
                />
                <p class="text-xs text-zinc-500 mt-2 text-center">{{ __('pages.blocks.image.enter_to_save') }}</p>
            </div>
        </template>
    </div>
@elseif ($preview)
    {{-- Preview with image --}}
    <figure
        x-data="{
            editing: false,
            url: @js($src),
            save() {
                if (this.url.trim() !== @js($src)) {
                    $wire.updateInlineText('{{ $block->id }}', 'src', this.url.trim());
                }
                this.editing = false;
            }
        }"
    >
        <div class="relative group">
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                class="w-full rounded-lg"
                x-show="!editing"
            />

            {{-- Edit overlay --}}
            <div
                x-show="!editing"
                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center cursor-pointer"
                x-on:click.stop="editing = true; $nextTick(() => $refs.urlInput.focus())"
            >
                <div class="text-white text-center">
                    <flux:icon name="pencil" class="size-6 mx-auto" />
                    <span class="text-sm mt-1 block">{{ __('pages.blocks.image.click_to_change') }}</span>
                </div>
            </div>

            {{-- URL input for editing --}}
            <div x-show="editing" class="p-4 rounded-lg border border-zinc-200 dark:border-zinc-700" x-on:click.stop>
                <flux:input
                    x-ref="urlInput"
                    x-model="url"
                    type="url"
                    placeholder="{{ __('pages.blocks.image.url_placeholder') }}"
                    x-on:keydown.enter.prevent="save()"
                    x-on:keydown.escape.prevent="editing = false; url = @js($src)"
                    x-on:blur="save()"
                />
                <p class="text-xs text-zinc-500 mt-2 text-center">{{ __('pages.blocks.image.enter_to_save') }}</p>
            </div>
        </div>

        @if ($caption)
            <figcaption class="text-center text-sm text-zinc-500 mt-2">{{ $caption }}</figcaption>
        @endif
    </figure>
@else
    {{-- Public display --}}
    @if (!empty($src))
        <figure>
            <img src="{{ $src }}" alt="{{ $alt }}" class="w-full rounded-lg" />
            @if ($caption)
                <figcaption class="text-center text-sm text-zinc-500 mt-2">{{ $caption }}</figcaption>
            @endif
        </figure>
    @endif
@endif
