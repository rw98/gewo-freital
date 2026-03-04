<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700" x-data="{
    columns: @entangle('editingField.config.columns').live,
    setLayout(layout) {
        this.columns = layout;
    },
    isActive(layout) {
        return JSON.stringify(this.columns) === JSON.stringify(layout);
    }
}">
    <flux:heading size="sm">{{ __('forms.builder.row_options') }}</flux:heading>

    <flux:description>{{ __('forms.builder.row_columns_description') }}</flux:description>

    <div class="grid grid-cols-2 gap-2">
        {{-- 1 Column --}}
        <button
            type="button"
            class="p-3 border rounded-lg transition-colors"
            :class="isActive([3]) ? 'border-accent bg-accent/10' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent'"
            x-on:click="setLayout([3])"
        >
            <div class="h-8 bg-zinc-200 dark:bg-zinc-600 rounded"></div>
            <span class="text-xs text-zinc-500 mt-1 block">{{ __('forms.builder.row_column_1') }}</span>
        </button>

        {{-- 2 Equal Columns --}}
        <button
            type="button"
            class="p-3 border rounded-lg transition-colors"
            :class="isActive([1,1]) ? 'border-accent bg-accent/10' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent'"
            x-on:click="setLayout([1,1])"
        >
            <div class="grid grid-cols-2 gap-1 h-8">
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
            </div>
            <span class="text-xs text-zinc-500 mt-1 block">{{ __('forms.builder.row_column_2') }}</span>
        </button>

        {{-- 3 Equal Columns --}}
        <button
            type="button"
            class="p-3 border rounded-lg transition-colors"
            :class="isActive([1,1,1]) ? 'border-accent bg-accent/10' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent'"
            x-on:click="setLayout([1,1,1])"
        >
            <div class="grid grid-cols-3 gap-1 h-8">
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
            </div>
            <span class="text-xs text-zinc-500 mt-1 block">{{ __('forms.builder.row_column_3') }}</span>
        </button>

        {{-- 2/3 + 1/3 --}}
        <button
            type="button"
            class="p-3 border rounded-lg transition-colors"
            :class="isActive([2,1]) ? 'border-accent bg-accent/10' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent'"
            x-on:click="setLayout([2,1])"
        >
            <div class="grid grid-cols-3 gap-1 h-8">
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded col-span-2"></div>
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
            </div>
            <span class="text-xs text-zinc-500 mt-1 block">{{ __('forms.builder.row_column_2_1') }}</span>
        </button>

        {{-- 1/3 + 2/3 --}}
        <button
            type="button"
            class="p-3 border rounded-lg transition-colors"
            :class="isActive([1,2]) ? 'border-accent bg-accent/10' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent'"
            x-on:click="setLayout([1,2])"
        >
            <div class="grid grid-cols-3 gap-1 h-8">
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded"></div>
                <div class="bg-zinc-200 dark:bg-zinc-600 rounded col-span-2"></div>
            </div>
            <span class="text-xs text-zinc-500 mt-1 block">{{ __('forms.builder.row_column_1_2') }}</span>
        </button>
    </div>
</div>
