@props(['block'])

<div class="space-y-4" x-data="{ plans: @entangle('editingContent.plans').live }" x-init="plans = plans || []">
    <flux:heading size="sm">{{ __('pages.blocks.pricing_table.plans') }}</flux:heading>

    <template x-for="(plan, index) in (plans || [])" :key="index">
        <div class="p-3 border border-zinc-200 dark:border-zinc-600 rounded-lg space-y-2">
            <div class="flex items-center justify-between">
                <flux:text size="sm" class="font-medium">{{ __('pages.blocks.pricing_table.plan') }} <span x-text="index + 1"></span></flux:text>
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1 text-sm">
                        <input type="checkbox" x-model="plan.featured" x-on:change="$wire.updateBlockContent('{{ $block->id }}')" class="rounded" />
                        {{ __('pages.blocks.pricing_table.featured') }}
                    </label>
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        x-on:click="plans.splice(index, 1); $wire.updateBlockContent('{{ $block->id }}')"
                    />
                </div>
            </div>
            <flux:input
                x-model="plan.name"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.pricing_table.name_placeholder')"
            />
            <div class="grid grid-cols-2 gap-2">
                <flux:input
                    x-model="plan.price"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    :placeholder="__('pages.blocks.pricing_table.price_placeholder')"
                />
                <flux:input
                    x-model="plan.period"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    :placeholder="__('pages.blocks.pricing_table.period_placeholder')"
                />
            </div>
            <flux:textarea
                x-model="plan.features_text"
                x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                :placeholder="__('pages.blocks.pricing_table.features_placeholder')"
                rows="3"
            />
            <div class="grid grid-cols-2 gap-2">
                <flux:input
                    x-model="plan.button_text"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    :placeholder="__('pages.blocks.pricing_table.button_text_placeholder')"
                />
                <flux:input
                    x-model="plan.url"
                    x-on:change="$wire.updateBlockContent('{{ $block->id }}')"
                    placeholder="https://"
                />
            </div>
        </div>
    </template>

    <flux:button
        variant="ghost"
        size="sm"
        icon="plus"
        x-on:click="plans.push({ name: '', price: '', period: '{{ __('pages.blocks.pricing_table.month') }}', features_text: '', button_text: '', url: '', featured: false }); $wire.updateBlockContent('{{ $block->id }}')"
    >
        {{ __('pages.blocks.pricing_table.add_plan') }}
    </flux:button>
</div>
