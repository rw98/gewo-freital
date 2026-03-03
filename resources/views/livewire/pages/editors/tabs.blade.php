@props(['block'])

<div class="space-y-4" x-data="{ tabs: @entangle('editingContent.tabs').live }" x-init="tabs = tabs || []">
    <flux:heading size="sm">{{ __('pages.blocks.tabs.tabs') }}</flux:heading>

    <template x-for="(tab, index) in (tabs || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.tabs.tab') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="tabs.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:input
                x-model="tab.title"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.tabs.title_placeholder')"
            />
            <flux:textarea
                x-model="tab.content"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.tabs.content_placeholder')"
                rows="3"
            />
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="tabs.push({ title: '', content: '' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.tabs.add_tab') }}
    </flux:button>
</div>
