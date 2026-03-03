@props(['block'])

<div class="space-y-4" x-data="{ features: @entangle('editingContent.features').live }" x-init="features = features || []">
    <flux:heading size="sm">{{ __('pages.blocks.feature_grid.features') }}</flux:heading>

    <template x-for="(feature, index) in (features || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.feature_grid.feature') }} <span x-text="index + 1"></span></flux:text>
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    x-on:click="features.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                />
            </div>
            <flux:input
                x-model="feature.icon"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.feature_grid.icon_placeholder')"
            />
            <flux:input
                x-model="feature.title"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.feature_grid.title_placeholder')"
            />
            <flux:textarea
                x-model="feature.description"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.feature_grid.description_placeholder')"
                rows="2"
            />
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="features.push({ icon: 'star', title: '', description: '' }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.feature_grid.add_feature') }}
    </flux:button>
</div>
