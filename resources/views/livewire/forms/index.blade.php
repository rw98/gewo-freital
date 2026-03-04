<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('forms.index.title') }}</flux:heading>
            <flux:text class="mt-1">{{ __('forms.index.description') }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" wire:click="openAiImportModal" icon="sparkles">
                {{ __('forms.index.import_pdf') }}
            </flux:button>
            <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
                {{ __('forms.index.create') }}
            </flux:button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-wrap items-center gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('forms.index.search_placeholder')"
            class="w-64"
        />

        <flux:select wire:model.live="status" class="w-40">
            <flux:select.option value="">{{ __('forms.index.all_statuses') }}</flux:select.option>
            <flux:select.option value="active">{{ __('forms.index.status_active') }}</flux:select.option>
            <flux:select.option value="inactive">{{ __('forms.index.status_inactive') }}</flux:select.option>
        </flux:select>
    </div>

    {{-- Forms table --}}
    <flux:card class="overflow-hidden">
        @if ($forms->isEmpty())
            <div class="p-12 text-center">
                <flux:icon name="document-text" class="size-12 text-zinc-300 mx-auto" />
                <flux:heading size="lg" class="mt-4">{{ __('forms.index.empty.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('forms.index.empty.description') }}</flux:text>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <flux:button variant="ghost" wire:click="openAiImportModal" icon="sparkles">
                        {{ __('forms.index.import_pdf') }}
                    </flux:button>
                    <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
                        {{ __('forms.index.empty.create') }}
                    </flux:button>
                </div>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('forms.index.columns.name') }}</flux:table.column>
                    <flux:table.column>{{ __('forms.index.columns.fields') }}</flux:table.column>
                    <flux:table.column>{{ __('forms.index.columns.responses') }}</flux:table.column>
                    <flux:table.column>{{ __('forms.index.columns.status') }}</flux:table.column>
                    <flux:table.column>{{ __('forms.index.columns.creator') }}</flux:table.column>
                    <flux:table.column>{{ __('forms.index.columns.updated_at') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($forms as $form)
                        <flux:table.row wire:key="form-{{ $form->id }}">
                            <flux:table.cell>
                                <a href="{{ route('forms.builder', $form) }}" class="font-medium hover:text-accent">
                                    {{ $form->name }}
                                </a>
                                @if ($form->description)
                                    <flux:text size="sm" class="text-zinc-500 truncate max-w-xs">
                                        {{ Str::limit($form->description, 50) }}
                                    </flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm">{{ $form->fields_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="blue">{{ $form->responses_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$form->is_active ? 'green' : 'zinc'" size="sm">
                                    {{ $form->is_active ? __('forms.index.active') : __('forms.index.inactive') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $form->creator?->name ?? '-' }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm" class="text-zinc-500">
                                    {{ $form->updated_at->diffForHumans() }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />

                                    <flux:menu>
                                        <flux:menu.item icon="pencil" href="{{ route('forms.builder', $form) }}">
                                            {{ __('forms.index.actions.edit') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="link" x-on:click="navigator.clipboard.writeText('{{ $form->getPublicUrl() }}'); $flux.toast('{{ __('forms.index.link_copied') }}')">
                                            {{ __('forms.index.actions.copy_link') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="document-duplicate" wire:click="duplicateForm('{{ $form->id }}')">
                                            {{ __('forms.index.actions.duplicate') }}
                                        </flux:menu.item>
                                        <flux:menu.item
                                            :icon="$form->is_active ? 'eye-slash' : 'eye'"
                                            wire:click="toggleActive('{{ $form->id }}')"
                                        >
                                            {{ $form->is_active ? __('forms.index.actions.deactivate') : __('forms.index.actions.activate') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item
                                            icon="trash"
                                            variant="danger"
                                            wire:click="deleteForm('{{ $form->id }}')"
                                            wire:confirm="{{ __('forms.index.confirm_delete') }}"
                                        >
                                            {{ __('forms.index.actions.delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $forms->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Create Modal --}}
    <flux:modal wire:model="showCreateModal" class="max-w-lg">
        <flux:heading size="lg">{{ __('forms.index.create_modal.title') }}</flux:heading>
        <flux:text class="mt-1">{{ __('forms.index.create_modal.description') }}</flux:text>

        <form wire:submit="createForm" class="mt-6 space-y-4">
            <flux:input
                wire:model="newFormName"
                :label="__('forms.index.create_modal.name')"
                :placeholder="__('forms.index.create_modal.name_placeholder')"
                required
            />

            <flux:textarea
                wire:model="newFormDescription"
                :label="__('forms.index.create_modal.description_label')"
                :placeholder="__('forms.index.create_modal.description_placeholder')"
                rows="3"
            />

            <div class="flex justify-end gap-3 pt-4">
                <flux:button variant="ghost" wire:click="$set('showCreateModal', false)">
                    {{ __('forms.index.create_modal.cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('forms.index.create_modal.create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- AI Import Modal --}}
    <flux:modal wire:model="showAiImportModal" class="max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center size-10 rounded-full bg-accent/10">
                <flux:icon name="sparkles" class="size-5 text-accent" />
            </div>
            <div>
                <flux:heading size="lg">{{ __('forms.ai.modal_title') }}</flux:heading>
                <flux:text size="sm" class="text-zinc-500">{{ __('forms.ai.modal_description') }}</flux:text>
            </div>
        </div>

        @if ($aiError)
            <flux:callout variant="danger" icon="exclamation-circle" class="mb-4" dismissible wire:click="$set('aiError', null)">
                {{ $aiError }}
            </flux:callout>
        @endif

        <form wire:submit="importFromPdf" class="space-y-4">
            <flux:input
                wire:model="aiFormName"
                :label="__('forms.ai.form_name')"
                :placeholder="__('forms.ai.form_name_placeholder')"
                required
            />

            <flux:field>
                <flux:label>{{ __('forms.ai.pdf_file') }}</flux:label>
                <div
                    x-data="{ dragging: false }"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    class="relative"
                >
                    <input
                        type="file"
                        wire:model="pdfFile"
                        accept=".pdf"
                        x-ref="fileInput"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    />
                    <div
                        class="flex flex-col items-center justify-center p-8 border-2 border-dashed rounded-lg transition-colors"
                        :class="dragging ? 'border-accent bg-accent/5' : 'border-zinc-300 hover:border-zinc-400'"
                    >
                        @if ($pdfFile && method_exists($pdfFile, 'getClientOriginalName'))
                            <flux:icon name="document-check" class="size-10 text-green-500 mb-2" />
                            <flux:text class="font-medium">{{ $pdfFile->getClientOriginalName() }}</flux:text>
                            @php
                                try {
                                    $fileSize = round($pdfFile->getSize() / 1024);
                                } catch (\Exception $e) {
                                    $fileSize = null;
                                }
                            @endphp
                            @if ($fileSize)
                                <flux:text size="sm" class="text-zinc-500">{{ $fileSize }} KB</flux:text>
                            @endif
                        @else
                            <flux:icon name="document-arrow-up" class="size-10 text-zinc-400 mb-2" />
                            <flux:text class="font-medium">{{ __('forms.ai.drop_pdf') }}</flux:text>
                            <flux:text size="sm" class="text-zinc-500">{{ __('forms.ai.or_click') }}</flux:text>
                        @endif
                    </div>
                </div>
                <flux:description>{{ __('forms.ai.pdf_description') }}</flux:description>
                <flux:error name="pdfFile" />
            </flux:field>

            <flux:textarea
                wire:model="aiPrompt"
                :label="__('forms.ai.additional_instructions')"
                :placeholder="__('forms.ai.additional_instructions_placeholder')"
                rows="3"
            />
            <flux:description>{{ __('forms.ai.additional_instructions_description') }}</flux:description>

            <div class="flex justify-end gap-3 pt-4">
                <flux:button variant="ghost" wire:click="$set('showAiImportModal', false)" :disabled="$isProcessing">
                    {{ __('forms.ai.cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" :disabled="$isProcessing || !$pdfFile">
                    @if ($isProcessing)
                        <flux:icon name="arrow-path" class="size-4 animate-spin mr-2" />
                        {{ __('forms.ai.processing') }}
                    @else
                        <flux:icon name="sparkles" class="size-4 mr-2" />
                        {{ __('forms.ai.generate') }}
                    @endif
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
