<div class="min-h-screen bg-zinc-100 dark:bg-zinc-900" x-data="{ addToRow: null, addToColumn: 0 }">
    {{-- Toolbar --}}
    <header class="sticky top-0 z-50 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between h-14 px-4">
            {{-- Left side --}}
            <div class="flex items-center gap-4">
                <flux:button variant="ghost" size="sm" href="{{ route('forms.index') }}" icon="arrow-left">
                    {{ __('forms.builder.back') }}
                </flux:button>

                <flux:separator vertical class="h-6" />

                <div class="flex items-center gap-2">
                    <flux:input
                        wire:model.blur="formName"
                        class="font-semibold border-transparent hover:border-zinc-300 focus:border-accent"
                    />
                    <flux:badge :color="$form->is_active ? 'green' : 'zinc'" size="sm">
                        {{ $form->is_active ? __('forms.builder.active') : __('forms.builder.inactive') }}
                    </flux:badge>
                </div>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="link"
                    x-on:click="navigator.clipboard.writeText('{{ $form->getPublicUrl() }}'); $flux.toast('{{ __('forms.builder.link_copied') }}')"
                >
                    {{ __('forms.builder.copy_link') }}
                </flux:button>

                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="eye"
                    href="{{ $form->getPublicUrl() }}"
                    target="_blank"
                >
                    {{ __('forms.builder.preview') }}
                </flux:button>

                <flux:button
                    :variant="$isActive ? 'danger' : 'primary'"
                    size="sm"
                    wire:click="$set('isActive', {{ $isActive ? 'false' : 'true' }})"
                >
                    {{ $isActive ? __('forms.builder.deactivate') : __('forms.builder.activate') }}
                </flux:button>
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Left Sidebar - Field Picker --}}
        <aside class="w-64 bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700 h-[calc(100vh-3.5rem)] overflow-y-auto sticky top-14">
            <div class="p-4">
                <flux:heading size="sm" class="mb-4">{{ __('forms.builder.field_types') }}</flux:heading>

                <div class="space-y-2">
                    @foreach ($this->fieldTypes as $type)
                        <button
                            wire:click="addField('{{ $type->value }}')"
                            class="flex items-center gap-3 w-full p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-left"
                        >
                            <flux:icon :name="$type->icon()" class="size-5 text-zinc-600 dark:text-zinc-400" />
                            <flux:text>{{ $type->label() }}</flux:text>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Canvas --}}
        <main class="flex-1 p-8 overflow-y-auto h-[calc(100vh-3.5rem)]">
            <div class="mx-auto max-w-2xl">
                {{-- Form Settings Card --}}
                <flux:card class="mb-6">
                    <div class="p-4 space-y-4">
                        <flux:heading size="sm">{{ __('forms.builder.form_settings') }}</flux:heading>

                        <flux:textarea
                            wire:model.blur="formDescription"
                            :label="__('forms.builder.description')"
                            :placeholder="__('forms.builder.description_placeholder')"
                            rows="2"
                        />

                        <flux:textarea
                            wire:model.blur="successMessage"
                            :label="__('forms.builder.success_message')"
                            :placeholder="__('forms.builder.success_message_placeholder')"
                            rows="2"
                        />

                        <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <flux:icon name="link" class="size-5 text-zinc-500" />
                            <flux:text size="sm" class="truncate flex-1">{{ $form->getPublicUrl() }}</flux:text>
                            <flux:button
                                variant="ghost"
                                size="xs"
                                icon="clipboard"
                                x-on:click="navigator.clipboard.writeText('{{ $form->getPublicUrl() }}'); $flux.toast('{{ __('forms.builder.link_copied') }}')"
                            />
                        </div>
                    </div>
                </flux:card>

                {{-- Fields --}}
                <flux:card class="overflow-hidden">
                    @if ($this->topLevelFields->isEmpty())
                        <div class="p-12 text-center">
                            <flux:icon name="list-bullet" class="size-12 text-zinc-300 mx-auto" />
                            <flux:heading size="lg" class="mt-4">{{ __('forms.builder.empty.title') }}</flux:heading>
                            <flux:text class="mt-2">{{ __('forms.builder.empty.description') }}</flux:text>
                        </div>
                    @else
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700" wire:sort="handleSort">
                            @foreach ($this->topLevelFields as $field)
                                @if ($field->type->value === 'row')
                                    {{-- Row Layout --}}
                                    @include('livewire.forms.partials.row-field', ['row' => $field])
                                @else
                                    {{-- Regular Field --}}
                                    @include('livewire.forms.partials.field-item', ['field' => $field])
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Add Field Button --}}
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button variant="ghost" class="w-full" wire:click="$set('showFieldPicker', true)" icon="plus">
                            {{ __('forms.builder.add_field') }}
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </main>

        {{-- Right Sidebar - Field Editor --}}
        @if ($selectedFieldId)
            @php $selectedField = \App\Models\FormField::find($selectedFieldId); @endphp
            @if ($selectedField)
                <aside class="w-80 bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 h-[calc(100vh-3.5rem)] overflow-y-auto sticky top-14">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="sm">{{ $selectedField->type->label() }}</flux:heading>
                            <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="deselectField" />
                        </div>

                        <div class="space-y-4">
                            @if ($selectedField->type->value !== 'row')
                                <flux:input
                                    wire:model.live.debounce.500ms="editingField.label"
                                    :label="__('forms.builder.field_label')"
                                />

                                <flux:input
                                    wire:model.live.debounce.500ms="editingField.name"
                                    :label="__('forms.builder.field_name')"
                                    :description="__('forms.builder.field_name_description')"
                                />

                                <flux:textarea
                                    wire:model.live.debounce.500ms="editingField.description"
                                    :label="__('forms.builder.field_description')"
                                    rows="2"
                                />

                                @if ($selectedField->type->requiresInput())
                                    <flux:input
                                        wire:model.live.debounce.500ms="editingField.placeholder"
                                        :label="__('forms.builder.field_placeholder')"
                                    />

                                    <flux:checkbox
                                        wire:model.live="editingField.is_required"
                                        :label="__('forms.builder.field_required')"
                                    />
                                @endif
                            @endif

                            {{-- Type-specific config --}}
                            @include('livewire.forms.editors.'.$selectedField->type->value, ['field' => $selectedField])

                            {{-- Auto-fill config (only for input fields) --}}
                            @if ($selectedField->type->requiresInput())
                                @include('livewire.forms.editors.autofill', [
                                    'sourceTypes' => $this->autoFillSourceTypes,
                                    'sourceFields' => $this->autoFillSourceFields,
                                ])
                            @endif
                        </div>
                    </div>
                </aside>
            @endif
        @endif
    </div>

    {{-- Field Picker Modal --}}
    <flux:modal wire:model="showFieldPicker" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">{{ __('forms.builder.add_field') }}</flux:heading>

            <div class="space-y-2">
                @foreach ($this->fieldTypes as $type)
                    <button
                        wire:click="addField('{{ $type->value }}', addToRow, addToColumn)"
                        x-on:click="addToRow = null; addToColumn = 0"
                        class="flex items-center gap-3 w-full p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-left"
                    >
                        <flux:icon :name="$type->icon()" class="size-5 text-zinc-600 dark:text-zinc-400" />
                        <div>
                            <flux:text class="font-medium">{{ $type->label() }}</flux:text>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="flex justify-end mt-6">
                <flux:button variant="ghost" wire:click="$set('showFieldPicker', false)" x-on:click="addToRow = null; addToColumn = 0">
                    {{ __('forms.builder.cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
