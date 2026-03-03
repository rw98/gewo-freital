@props(['block'])

<div class="space-y-4" x-data="{ images: @entangle('editingContent.images').live }" x-init="images = images || []">
    <flux:heading size="sm">{{ __('pages.blocks.image_gallery.images') }}</flux:heading>

    <template x-for="(image, index) in (images || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.image_gallery.image') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="images.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:input
                x-model="image.src"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.image.url_placeholder')"
            />
            <flux:input
                x-model="image.alt"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.image.alt_placeholder')"
            />
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="images.push({ src: '', alt: '' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.image_gallery.add_image') }}
    </flux:button>
</div>
