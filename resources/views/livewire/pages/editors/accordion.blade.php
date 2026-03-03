@props(['block'])

<div class="space-y-4" x-data="{ items: @entangle('editingContent.items').live }" x-init="items = items || []">
    <flux:heading size="sm">{{ __('pages.blocks.accordion.items') }}</flux:heading>

    <template x-for="(item, index) in (items || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.accordion.item') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="items.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:input
                x-model="item.title"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.accordion.title_placeholder')"
            />
            <flux:textarea
                x-model="item.content"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.accordion.content_placeholder')"
                rows="2"
            />
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="items.push({ title: '', content: '' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.accordion.add_item') }}
    </flux:button>
</div>
