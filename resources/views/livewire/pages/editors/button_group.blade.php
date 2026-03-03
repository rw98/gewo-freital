@props(['block'])

<div class="space-y-4" x-data="{ buttons: @entangle('editingContent.buttons').live }" x-init="buttons = buttons || []">
    <flux:heading size="sm">{{ __('pages.blocks.button_group.buttons') }}</flux:heading>

    <template x-for="(button, index) in (buttons || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.button_group.button') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="buttons.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:input
                x-model="button.text"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.button.text_placeholder')"
            />
            <flux:input
                x-model="button.url"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                placeholder="https://"
            />
            <flux:select
                x-model="button.variant"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
            >
                <flux:select.option value="primary">{{ __('pages.blocks.button.variants.primary') }}</flux:select.option>
                <flux:select.option value="filled">{{ __('pages.blocks.button.variants.filled') }}</flux:select.option>
                <flux:select.option value="ghost">{{ __('pages.blocks.button.variants.ghost') }}</flux:select.option>
            </flux:select>
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="buttons.push({ text: '', url: '', variant: 'primary', size: 'base' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.button_group.add_button') }}
    </flux:button>
</div>
