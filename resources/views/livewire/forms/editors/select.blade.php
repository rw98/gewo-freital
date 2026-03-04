<div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700" x-data="{
    options: @entangle('editingField.config.options').live,
    newOption: '',
    addOption() {
        if (this.newOption.trim()) {
            this.options.push({ label: this.newOption.trim(), value: this.newOption.trim().toLowerCase().replace(/\s+/g, '_') });
            this.newOption = '';
        }
    },
    removeOption(index) {
        this.options.splice(index, 1);
    }
}">
    <flux:heading size="sm">{{ __('forms.builder.select_options') }}</flux:heading>

    <flux:checkbox
        wire:model.live="editingField.config.multiple"
        :label="__('forms.builder.allow_multiple')"
    />

    <div>
        <flux:label>{{ __('forms.builder.options') }}</flux:label>

        <div class="space-y-2 mt-2">
            <template x-for="(option, index) in options" :key="index">
                <div class="flex items-center gap-2">
                    <flux:input
                        x-model="option.label"
                        class="flex-1"
                        :placeholder="__('forms.builder.option_label')"
                    />
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        class="text-red-500"
                        x-on:click="removeOption(index)"
                    />
                </div>
            </template>
        </div>

        <div class="flex items-center gap-2 mt-2">
            <flux:input
                x-model="newOption"
                class="flex-1"
                :placeholder="__('forms.builder.new_option')"
                x-on:keydown.enter.prevent="addOption()"
            />
            <flux:button
                variant="ghost"
                size="sm"
                icon="plus"
                x-on:click="addOption()"
            />
        </div>
    </div>
</div>
