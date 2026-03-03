@props(['block'])

<div class="space-y-4" x-data="{ testimonials: @entangle('editingContent.testimonials').live }" x-init="testimonials = testimonials || []">
    <flux:heading size="sm">{{ __('pages.blocks.testimonials.testimonials') }}</flux:heading>

    <template x-for="(testimonial, index) in (testimonials || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.testimonials.testimonial') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="testimonials.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:textarea
                x-model="testimonial.quote"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.testimonials.quote_placeholder')"
                rows="2"
            />
            <div class="grid grid-cols-2 gap-2">
                <flux:input
                    x-model="testimonial.name"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    :placeholder="__('pages.blocks.testimonials.name_placeholder')"
                />
                <flux:input
                    x-model="testimonial.role"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    :placeholder="__('pages.blocks.testimonials.role_placeholder')"
                />
            </div>
            <flux:input
                x-model="testimonial.avatar"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.testimonials.avatar_placeholder')"
            />
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="testimonials.push({ quote: '', name: '', role: '', avatar: '' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.testimonials.add_testimonial') }}
    </flux:button>
</div>
