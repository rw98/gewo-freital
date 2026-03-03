@props(['block', 'preview' => false])

@php
    $text = $block->getContent('text', '');
    $placeholder = __('pages.blocks.paragraph.placeholder');
@endphp

@if ($preview)
    <p
        class="text-base text-zinc-700 dark:text-zinc-300 outline-none focus:ring-2 focus:ring-accent/50 rounded px-1 -mx-1 cursor-text whitespace-pre-wrap"
        :class="{ '!text-zinc-400 italic': isEmpty }"
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
        x-on:click.stop
    >{{ $text ?: $placeholder }}</p>
@elseif (empty($text))
    <span class="text-zinc-400 italic">{{ $placeholder }}</span>
@else
    <flux:text>
        {{ $text }}
    </flux:text>
@endif
