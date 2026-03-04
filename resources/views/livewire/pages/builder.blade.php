<div class="min-h-screen bg-zinc-100 dark:bg-zinc-900" x-data="{
    sidebarOpen: true,
    draggingBlockType: null,
    draggingBlockId: null,
    dragOverTarget: null
}">
    {{-- Toolbar --}}
    <header class="sticky top-0 z-50 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between h-14 px-4">
            {{-- Left side --}}
            <div class="flex items-center gap-4">
                <flux:button variant="ghost" size="sm" href="{{ route('pages.index') }}" icon="arrow-left">
                    {{ __('pages.builder.back') }}
                </flux:button>

                <flux:separator vertical class="h-6" />

                <div class="flex items-center gap-2">
                    <flux:input
                        wire:model.blur="pageTitle"
                        class="font-semibold border-transparent hover:border-zinc-300 focus:border-accent"
                    />
                    <flux:badge :color="$page->status->color()" size="sm">
                        {{ $page->status->label() }}
                    </flux:badge>
                </div>
            </div>

            {{-- Center - Preview modes --}}
            <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-700 rounded-lg p-1">
                <flux:button
                    variant="{{ $previewMode === 'desktop' ? 'filled' : 'ghost' }}"
                    size="sm"
                    icon="computer-desktop"
                    wire:click="setPreviewMode('desktop')"
                />
                <flux:button
                    variant="{{ $previewMode === 'tablet' ? 'filled' : 'ghost' }}"
                    size="sm"
                    icon="device-tablet"
                    wire:click="setPreviewMode('tablet')"
                />
                <flux:button
                    variant="{{ $previewMode === 'mobile' ? 'filled' : 'ghost' }}"
                    size="sm"
                    icon="device-phone-mobile"
                    wire:click="setPreviewMode('mobile')"
                />
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">
                @if ($page->isPublished())
                    <flux:button variant="ghost" size="sm" href="{{ route('pages.show', $page->slug) }}" target="_blank" icon="eye">
                        {{ __('pages.builder.view') }}
                    </flux:button>
                @endif

                <flux:button variant="ghost" size="sm" wire:click="savePage" icon="check">
                    {{ __('pages.builder.save') }}
                </flux:button>

                @if ($page->isPublished())
                    <flux:button variant="danger" size="sm" wire:click="unpublishPage" wire:confirm="{{ __('pages.builder.confirm_unpublish') }}">
                        {{ __('pages.builder.unpublish') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" size="sm" wire:click="publishPage">
                        {{ __('pages.builder.publish') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Left Sidebar - Block Picker --}}
        <aside class="w-64 bg-white dark:bg-zinc-800 border-r border-zinc-200 dark:border-zinc-700 h-[calc(100vh-3.5rem)] overflow-y-auto sticky top-14">
            <div class="p-4">
                <flux:heading size="sm" class="mb-4">{{ __('pages.builder.blocks') }}</flux:heading>

                @foreach ($this->blockTypes as $category => $types)
                    <div class="mb-4">
                        <flux:text size="xs" class="uppercase tracking-wide text-zinc-500 mb-2">
                            {{ __('pages.builder.categories.'.$category) }}
                        </flux:text>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($types as $type)
                                <button
                                    draggable="true"
                                    x-on:dragstart="draggingBlockType = '{{ $type->value }}'; $event.dataTransfer.effectAllowed = 'copy'"
                                    x-on:dragend="draggingBlockType = null; dragOverTarget = null"
                                    wire:click="addBlock('{{ $type->value }}')"
                                    class="flex flex-col items-center gap-1 p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-center"
                                    :class="{ 'opacity-50': draggingBlockType === '{{ $type->value }}' }"
                                >
                                    <flux:icon :name="$type->icon()" class="size-5 text-zinc-600 dark:text-zinc-400" />
                                    <flux:text size="xs">{{ $type->label() }}</flux:text>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <flux:separator class="my-4" />

                <flux:button variant="ghost" size="sm" class="w-full" wire:click="$set('showTemplatePicker', true)" icon="document-duplicate">
                    {{ __('pages.builder.apply_template') }}
                </flux:button>
            </div>
        </aside>

        {{-- Canvas --}}
        <main class="flex-1 p-8 overflow-y-auto h-[calc(100vh-3.5rem)]">
            <div
                class="mx-auto bg-white dark:bg-zinc-800 shadow-lg rounded-lg min-h-[600px] transition-all duration-300"
                style="max-width: {{ $previewMode === 'desktop' ? '1152px' : ($previewMode === 'tablet' ? '768px' : '375px') }}"
            >
                @if ($page->blocks->isEmpty())
                    {{-- Empty state --}}
                    <div
                        class="flex flex-col items-center justify-center h-[600px] text-center p-8 transition-colors"
                        x-on:dragover.prevent="dragOverTarget = 'empty'; $event.dataTransfer.dropEffect = 'copy'"
                        x-on:dragleave="dragOverTarget = null"
                        x-on:drop.prevent="if (draggingBlockType) { $wire.addBlock(draggingBlockType); draggingBlockType = null; dragOverTarget = null; }"
                        :class="{ 'ring-2 ring-accent ring-inset bg-accent/5': draggingBlockType && dragOverTarget === 'empty' }"
                    >
                        <flux:icon name="squares-plus" class="size-16 text-zinc-300 dark:text-zinc-600" />
                        <flux:heading size="lg" class="mt-4">{{ __('pages.builder.empty.title') }}</flux:heading>
                        <flux:text class="mt-2 max-w-md">{{ __('pages.builder.empty.description') }}</flux:text>
                        <div class="flex gap-2 mt-6">
                            <flux:button variant="primary" wire:click="$set('showTemplatePicker', true)" icon="document-duplicate">
                                {{ __('pages.builder.empty.use_template') }}
                            </flux:button>
                            <flux:button variant="ghost" wire:click="addBlock('heading')" icon="plus">
                                {{ __('pages.builder.empty.start_blank') }}
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- Blocks --}}
                    <div
                        class="p-4 sm:p-6 transition-colors"
                        x-on:dragover.prevent="if (!$event.target.closest('[data-drop-indicator]') && !$event.target.closest('[data-grid-drop]')) { dragOverTarget = 'canvas'; $event.dataTransfer.dropEffect = 'copy'; }"
                        x-on:dragleave.self="dragOverTarget = null"
                        x-on:drop.prevent="if (draggingBlockType && dragOverTarget === 'canvas') { $wire.addBlock(draggingBlockType); draggingBlockType = null; dragOverTarget = null; }"
                        :class="{ 'ring-2 ring-accent ring-inset bg-accent/5': draggingBlockType && dragOverTarget === 'canvas' }"
                    >
                        {{-- Drop indicator before first block --}}
                        <div
                            x-show="draggingBlockType"
                            x-cloak
                            data-drop-indicator
                            class="h-1 bg-accent rounded-full mb-2 opacity-0 transition-opacity"
                            x-on:dragover.prevent.stop="dragOverTarget = 'before-first'; $event.dataTransfer.dropEffect = 'copy'"
                            x-on:dragleave.stop="dragOverTarget = null"
                            x-on:drop.prevent.stop="$wire.addBlockAtPosition(draggingBlockType, 0); draggingBlockType = null; dragOverTarget = null"
                            :class="{ 'opacity-100': dragOverTarget === 'before-first' }"
                        ></div>

                        <div class="grid grid-cols-12 gap-4" wire:sort="handleSort">
                            @foreach ($page->blocks as $block)
                                @php
                                    $span = $block->column_span;
                                    // Responsive column spans for preview
                                    $colClasses = match (true) {
                                        $span <= 4 => 'col-span-12 sm:col-span-6 md:col-span-' . $span,
                                        $span <= 6 => 'col-span-12 sm:col-span-' . $span,
                                        $span <= 8 => 'col-span-12 md:col-span-' . $span,
                                        default => 'col-span-12',
                                    };
                                @endphp
                                <div
                                    wire:key="block-{{ $block->id }}"
                                    wire:sort:item="{{ $block->id }}"
                                    class="group relative {{ $colClasses }} rounded-lg border-2 transition-colors {{ $selectedBlockId === $block->id ? 'border-accent ring-2 ring-accent/20' : 'border-transparent hover:border-zinc-200 dark:hover:border-zinc-600' }}"
                                >
                                    {{-- Block toolbar --}}
                                    <div class="absolute -top-3 left-4 hidden group-hover:flex items-center gap-1 bg-white dark:bg-zinc-700 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-600 px-1 py-0.5 z-10">
                                        <div wire:sort:handle class="cursor-move px-1 text-zinc-400 hover:text-zinc-600" title="{{ __('pages.builder.drag_to_reorder') }}">
                                            <flux:icon name="bars-3" class="size-4" />
                                        </div>
                                        <div
                                            draggable="true"
                                            x-on:dragstart="draggingBlockId = '{{ $block->id }}'; $event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('text/plain', '{{ $block->id }}')"
                                            x-on:dragend="draggingBlockId = null; dragOverTarget = null"
                                            class="cursor-grab px-1 text-zinc-400 hover:text-accent"
                                            title="{{ __('pages.builder.drag_to_container') }}"
                                        >
                                            <flux:icon name="arrow-right-circle" class="size-4" />
                                        </div>
                                        <flux:text size="xs" class="text-zinc-500 px-1">{{ $block->type->label() }}</flux:text>
                                        <flux:separator vertical class="h-4" />
                                        <button wire:click="selectBlock('{{ $block->id }}')" class="p-1 text-zinc-400 hover:text-accent">
                                            <flux:icon name="pencil" class="size-3.5" />
                                        </button>
                                        <button wire:click="duplicateBlock('{{ $block->id }}')" class="p-1 text-zinc-400 hover:text-accent">
                                            <flux:icon name="document-duplicate" class="size-3.5" />
                                        </button>
                                        <button wire:click="deleteBlock('{{ $block->id }}')" wire:confirm="{{ __('pages.builder.confirm_delete_block') }}" class="p-1 text-zinc-400 hover:text-red-500">
                                            <flux:icon name="trash" class="size-3.5" />
                                        </button>
                                    </div>

                                    {{-- Block content --}}
                                    <div
                                        wire:click="selectBlock('{{ $block->id }}')"
                                        class="cursor-pointer p-4"
                                    >
                                        <x-pages.block-renderer :block="$block" :preview="true" />
                                    </div>

                                    {{-- Add block button --}}
                                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 hidden group-hover:block z-10">
                                        <button
                                            wire:click="openBlockPicker('{{ $block->id }}')"
                                            class="flex items-center justify-center size-6 rounded-full bg-accent text-white shadow-lg hover:bg-accent/90"
                                        >
                                            <flux:icon name="plus" class="size-4" />
                                        </button>
                                    </div>
                                </div>

                                {{-- Drop indicator after each block (spans full width) --}}
                                <div
                                    x-show="draggingBlockType"
                                    x-cloak
                                    data-drop-indicator
                                    class="col-span-12 h-1 bg-accent rounded-full opacity-0 transition-opacity"
                                    x-on:dragover.prevent.stop="dragOverTarget = 'after-{{ $block->id }}'; $event.dataTransfer.dropEffect = 'copy'"
                                    x-on:dragleave.stop="dragOverTarget = null"
                                    x-on:drop.prevent.stop="$wire.addBlockAtPosition(draggingBlockType, {{ $loop->index + 1 }}); draggingBlockType = null; dragOverTarget = null"
                                    :class="{ 'opacity-100': dragOverTarget === 'after-{{ $block->id }}' }"
                                ></div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </main>

        {{-- Right Sidebar - Block Editor --}}
        @if ($selectedBlockId)
            @php $selectedBlock = \App\Models\PageBlock::find($selectedBlockId); @endphp
            @if ($selectedBlock)
                <aside class="w-80 bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 h-[calc(100vh-3.5rem)] overflow-y-auto sticky top-14">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="sm">{{ $selectedBlock->type->label() }}</flux:heading>
                            <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="deselectBlock" />
                        </div>

                        @if ($selectedBlock->type->supportsChildren())
                            <div class="mb-4 p-3 bg-accent/10 rounded-lg">
                                <flux:text size="sm" class="mb-2">{{ __('pages.builder.container_info') }}</flux:text>
                                <flux:button variant="primary" size="sm" wire:click="$set('addingToParentId', '{{ $selectedBlock->id }}')" icon="plus" class="w-full">
                                    {{ __('pages.builder.add_child_block') }}
                                </flux:button>

                                @if ($selectedBlock->children->count() > 0)
                                    <div class="mt-3 space-y-1">
                                        <flux:text size="xs" class="text-zinc-500">{{ __('pages.builder.child_blocks') }} ({{ $selectedBlock->children->count() }})</flux:text>
                                        @foreach ($selectedBlock->children as $child)
                                            <div class="flex items-center justify-between p-2 bg-white dark:bg-zinc-700 rounded text-sm">
                                                <span>{{ $child->type->label() }}</span>
                                                <div class="flex gap-1">
                                                    <button wire:click="selectBlock('{{ $child->id }}')" class="text-zinc-400 hover:text-accent">
                                                        <flux:icon name="pencil" class="size-3.5" />
                                                    </button>
                                                    <button wire:click="deleteBlock('{{ $child->id }}')" class="text-zinc-400 hover:text-red-500">
                                                        <flux:icon name="trash" class="size-3.5" />
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <flux:tab.group>
                            <flux:tabs>
                                <flux:tab name="content">{{ __('pages.builder.content') }}</flux:tab>
                                <flux:tab name="settings">{{ __('pages.builder.settings') }}</flux:tab>
                            </flux:tabs>

                            <flux:tab.panel name="content" class="pt-4">
                                @include('livewire.pages.editors.'.$selectedBlock->type->value, ['block' => $selectedBlock])
                            </flux:tab.panel>

                            <flux:tab.panel name="settings" class="pt-4 space-y-4">
                            {{-- Column span --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.column_span') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.column_span">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <flux:select.option value="{{ $i }}">{{ $i }}/12</flux:select.option>
                                    @endfor
                                </flux:select>
                            </flux:field>

                            {{-- Max width --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.max_width') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.max_width">
                                    <flux:select.option value="full">{{ __('pages.builder.max_width_full') }}</flux:select.option>
                                    <flux:select.option value="prose">{{ __('pages.builder.max_width_prose') }}</flux:select.option>
                                    <flux:select.option value="xs">{{ __('pages.builder.max_width_xs') }}</flux:select.option>
                                    <flux:select.option value="sm">{{ __('pages.builder.max_width_sm') }}</flux:select.option>
                                    <flux:select.option value="md">{{ __('pages.builder.max_width_md') }}</flux:select.option>
                                    <flux:select.option value="lg">{{ __('pages.builder.max_width_lg') }}</flux:select.option>
                                    <flux:select.option value="xl">{{ __('pages.builder.max_width_xl') }}</flux:select.option>
                                    <flux:select.option value="2xl">{{ __('pages.builder.max_width_2xl') }}</flux:select.option>
                                    <flux:select.option value="3xl">{{ __('pages.builder.max_width_3xl') }}</flux:select.option>
                                    <flux:select.option value="4xl">{{ __('pages.builder.max_width_4xl') }}</flux:select.option>
                                    <flux:select.option value="5xl">{{ __('pages.builder.max_width_5xl') }}</flux:select.option>
                                </flux:select>
                                <flux:description>{{ __('pages.builder.max_width_description') }}</flux:description>
                            </flux:field>

                            {{-- Padding --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.padding') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.padding">
                                    <flux:select.option value="none">{{ __('pages.builder.none') }}</flux:select.option>
                                    <flux:select.option value="sm">{{ __('pages.builder.small') }}</flux:select.option>
                                    <flux:select.option value="md">{{ __('pages.builder.medium') }}</flux:select.option>
                                    <flux:select.option value="lg">{{ __('pages.builder.large') }}</flux:select.option>
                                </flux:select>
                            </flux:field>

                            {{-- Margin --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.margin') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.margin">
                                    <flux:select.option value="none">{{ __('pages.builder.none') }}</flux:select.option>
                                    <flux:select.option value="sm">{{ __('pages.builder.small') }}</flux:select.option>
                                    <flux:select.option value="md">{{ __('pages.builder.medium') }}</flux:select.option>
                                    <flux:select.option value="lg">{{ __('pages.builder.large') }}</flux:select.option>
                                </flux:select>
                            </flux:field>

                            {{-- Text align --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.text_align') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.text_align">
                                    <flux:select.option value="left">{{ __('pages.builder.left') }}</flux:select.option>
                                    <flux:select.option value="center">{{ __('pages.builder.center') }}</flux:select.option>
                                    <flux:select.option value="right">{{ __('pages.builder.right') }}</flux:select.option>
                                </flux:select>
                            </flux:field>

                            {{-- Background --}}
                            <flux:field>
                                <flux:label>{{ __('pages.builder.background') }}</flux:label>
                                <flux:select wire:model.live="editingSettings.background">
                                    <flux:select.option value="transparent">{{ __('pages.builder.transparent') }}</flux:select.option>
                                    <flux:select.option value="white">{{ __('pages.builder.white') }}</flux:select.option>
                                    <flux:select.option value="gray">{{ __('pages.builder.gray') }}</flux:select.option>
                                    <flux:select.option value="primary">{{ __('pages.builder.primary') }}</flux:select.option>
                                </flux:select>
                            </flux:field>
                        </flux:tab.panel>
                        </flux:tab.group>
                    </div>
                </aside>
            @endif
        @endif
    </div>

    {{-- Template Picker Modal --}}
    <flux:modal wire:model="showTemplatePicker" class="max-w-3xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">{{ __('pages.builder.choose_template') }}</flux:heading>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($this->templates as $template)
                    <button
                        wire:click="applyTemplate('{{ $template->id }}')"
                        wire:confirm="{{ __('pages.builder.confirm_apply_template') }}"
                        class="flex flex-col items-center p-4 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-center"
                    >
                        @if ($template->thumbnail_path)
                            <img src="{{ Storage::url($template->thumbnail_path) }}" alt="{{ $template->name }}" class="w-full h-32 object-cover rounded-lg mb-3" />
                        @else
                            <div class="w-full h-32 bg-zinc-100 dark:bg-zinc-700 rounded-lg mb-3 flex items-center justify-center">
                                <flux:icon :name="$template->category->icon()" class="size-8 text-zinc-400" />
                            </div>
                        @endif
                        <flux:heading size="sm">{{ $template->name }}</flux:heading>
                        <flux:text size="xs" class="text-zinc-500 mt-1">{{ $template->description }}</flux:text>
                        <flux:badge size="sm" class="mt-2">{{ $template->category->label() }}</flux:badge>
                    </button>
                @endforeach
            </div>

            <div class="flex justify-end mt-6">
                <flux:button variant="ghost" wire:click="$set('showTemplatePicker', false)">
                    {{ __('pages.builder.cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Block Picker Modal --}}
    <flux:modal wire:model="showBlockPicker" class="max-w-2xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">{{ __('pages.builder.add_block') }}</flux:heading>

            @foreach ($this->blockTypes as $category => $types)
                <div class="mb-4">
                    <flux:text size="xs" class="uppercase tracking-wide text-zinc-500 mb-2">
                        {{ __('pages.builder.categories.'.$category) }}
                    </flux:text>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($types as $type)
                            <button
                                wire:click="addBlock('{{ $type->value }}')"
                                class="flex flex-col items-center gap-1 p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-center"
                            >
                                <flux:icon :name="$type->icon()" class="size-5 text-zinc-600 dark:text-zinc-400" />
                                <flux:text size="xs">{{ $type->label() }}</flux:text>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end mt-6">
                <flux:button variant="ghost" wire:click="closeBlockPicker">
                    {{ __('pages.builder.cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Child Block Picker Modal --}}
    <flux:modal :show="$addingToParentId !== null" class="max-w-2xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">{{ __('pages.builder.add_child_block') }}</flux:heading>

            @foreach ($this->blockTypes as $category => $types)
                @if ($category !== 'sections')
                    <div class="mb-4">
                        <flux:text size="xs" class="uppercase tracking-wide text-zinc-500 mb-2">
                            {{ __('pages.builder.categories.'.$category) }}
                        </flux:text>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($types as $type)
                                @if (!$type->supportsChildren())
                                    <button
                                        wire:click="addBlock('{{ $type->value }}')"
                                        class="flex flex-col items-center gap-1 p-3 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-accent hover:bg-accent/5 transition-colors text-center"
                                    >
                                        <flux:icon :name="$type->icon()" class="size-5 text-zinc-600 dark:text-zinc-400" />
                                        <flux:text size="xs">{{ $type->label() }}</flux:text>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="flex justify-end mt-6">
                <flux:button variant="ghost" wire:click="$set('addingToParentId', null)">
                    {{ __('pages.builder.cancel') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
