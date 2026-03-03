@props(['block', 'preview' => false])

@php
    $html = $block->getContent('html', '');
    $placeholder = __('pages.blocks.rich_text.placeholder');
@endphp

@if ($preview)
    <div
        class="prose dark:prose-invert max-w-none outline-none focus:ring-2 focus:ring-accent/50 rounded px-1 -mx-1 cursor-text"
        :class="{ 'text-zinc-400 italic': isEmpty }"
        contenteditable="true"
        x-data="{
            html: @js($html),
            isEmpty: @js(empty($html)),
            placeholder: @js($placeholder),
            handleFocus() {
                if (this.isEmpty) {
                    this.$el.innerHTML = '<p><br></p>';
                    this.isEmpty = false;
                }
            },
            handleBlur() {
                const newHtml = this.$el.innerHTML.trim();
                const textContent = this.$el.textContent.trim();
                if (textContent === '' || textContent === this.placeholder) {
                    this.isEmpty = true;
                    this.$el.innerHTML = '<p>' + this.placeholder + '</p>';
                    if (this.html !== '') {
                        this.html = '';
                        $wire.updateInlineText('{{ $block->id }}', 'html', '');
                    }
                } else if (newHtml !== this.html) {
                    this.html = newHtml;
                    $wire.updateInlineText('{{ $block->id }}', 'html', newHtml);
                }
            }
        }"
        x-on:focus="handleFocus()"
        x-on:blur="handleBlur()"
        x-on:click.stop
    >{!! $html ?: '<p>' . $placeholder . '</p>' !!}</div>
@elseif (empty($html))
    <span class="text-zinc-400 italic">{{ $placeholder }}</span>
@else
    <div class="prose dark:prose-invert max-w-none">
        {!! $html !!}
    </div>
@endif
