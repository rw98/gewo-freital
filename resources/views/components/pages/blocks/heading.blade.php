@props(['block', 'preview' => false])

@php
    $text = $block->getContent('text', '');
    $level = $block->getContent('level', 2);

    $headingClasses = match ((int) $level) {
        1 => 'text-4xl font-bold',
        2 => 'text-3xl font-bold',
        3 => 'text-2xl font-semibold',
        4 => 'text-xl font-semibold',
        5 => 'text-lg font-medium',
        6 => 'text-base font-medium',
        default => 'text-3xl font-bold',
    };

    $tag = 'h' . $level;
    $placeholder = __('pages.blocks.heading.placeholder');
@endphp

@if ($preview)
    <{{ $tag }}
        class="{{ $headingClasses }} outline-none focus:ring-2 focus:ring-accent/50 rounded px-1 -mx-1 cursor-text"
        :class="{ 'text-zinc-400 italic font-normal': isEmpty }"
        contenteditable="true"
        x-data="{
            text: @js($text),
            isEmpty: @js(empty($text)),
            placeholder: @js($placeholder),
            handleFocus() {
                if (this.isEmpty) {
                    this.$el.innerText = '';
                    this.isEmpty = false;
                }
            },
            handleBlur() {
                const newText = this.$el.innerText.trim();
                if (newText === '' || newText === this.placeholder) {
                    this.isEmpty = true;
                    this.$el.innerText = this.placeholder;
                    if (this.text !== '') {
                        this.text = '';
                        $wire.updateInlineText('{{ $block->id }}', 'text', '');
                    }
                } else if (newText !== this.text) {
                    this.text = newText;
                    $wire.updateInlineText('{{ $block->id }}', 'text', newText);
                }
            }
        }"
        x-on:focus="handleFocus()"
        x-on:blur="handleBlur()"
        x-on:keydown.enter.prevent="$el.blur()"
        x-on:click.stop
    >{{ $text ?: $placeholder }}</{{ $tag }}>
@elseif (empty($text))
    <span class="text-zinc-400 italic">{{ $placeholder }}</span>
@else
    <flux:heading :level="$level">
        {{ $text }}
    </flux:heading>
@endif
