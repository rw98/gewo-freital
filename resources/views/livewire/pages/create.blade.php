<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <flux:button variant="ghost" href="{{ route('pages.index') }}" icon="arrow-left" class="mb-4">
            {{ __('pages.create.back') }}
        </flux:button>
        <flux:heading size="xl">{{ __('pages.create.title') }}</flux:heading>
        <flux:text class="mt-1">{{ __('pages.create.description') }}</flux:text>
    </div>

    <form wire:submit="create" class="space-y-8">
        {{-- Page details --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('pages.create.details') }}</flux:heading>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('pages.create.page_title') }}</flux:label>
                    <flux:input
                        wire:model.live="title"
                        :placeholder="__('pages.create.page_title_placeholder')"
                        autofocus
                    />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('pages.create.slug') }}</flux:label>
                    <div class="flex items-center">
                        <span class="text-zinc-500 mr-1">/p/</span>
                        <flux:input
                            wire:model="slug"
                            :placeholder="__('pages.create.slug_placeholder')"
                            class="flex-1"
                        />
                    </div>
                    <flux:error name="slug" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('pages.create.layout') }}</flux:label>
                    <flux:select wire:model="layout">
                        @foreach ($this->layouts as $layoutOption)
                            <flux:select.option value="{{ $layoutOption->value }}">{{ $layoutOption->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="layout" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Import from URL --}}
        <flux:card>
            <flux:heading size="lg" class="mb-2">{{ __('pages.create.import_url') }}</flux:heading>
            <flux:text class="mb-4">{{ __('pages.create.import_url_description') }}</flux:text>

            <div class="space-y-4">
                <div class="flex gap-2">
                    <flux:input
                        wire:model.live="importUrl"
                        type="url"
                        :placeholder="__('pages.create.import_url_placeholder')"
                        class="flex-1"
                        :disabled="$isImporting"
                    />
                    <flux:button
                        type="button"
                        wire:click="importFromUrl"
                        :disabled="$isImporting || empty($importUrl)"
                        icon="{{ $isImporting ? 'arrow-path' : 'arrow-down-tray' }}"
                        :class="$isImporting ? 'animate-pulse' : ''"
                    >
                        @if ($isImporting)
                            {{ __('pages.create.importing') }}
                        @else
                            {{ __('pages.create.import') }}
                        @endif
                    </flux:button>
                </div>

                <flux:error name="importUrl" />

                @if ($importError)
                    <flux:callout variant="danger" icon="exclamation-triangle">
                        {{ $importError }}
                    </flux:callout>
                @endif

                @if (count($importedBlocks) > 0)
                    <flux:callout variant="success" icon="check-circle">
                        <div class="flex items-center justify-between">
                            <span>
                                {{ __('pages.create.import_success', ['count' => count($importedBlocks)]) }}
                            </span>
                            <flux:button variant="ghost" size="sm" wire:click="clearImport" icon="x-mark">
                                {{ __('pages.create.clear_import') }}
                            </flux:button>
                        </div>
                    </flux:callout>

                    {{-- Preview imported blocks --}}
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 max-h-80 overflow-y-auto">
                        <flux:text size="sm" class="text-zinc-500 mb-2">{{ __('pages.create.import_preview') }}</flux:text>
                        <div class="space-y-2">
                            @foreach ($importedBlocks as $index => $block)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-zinc-400 shrink-0">{{ $index + 1 }}.</span>
                                    <flux:badge size="sm" class="shrink-0">{{ $block['type'] }}</flux:badge>
                                    @if ($block['type'] === 'image' && !empty($block['content']['src']))
                                        <img
                                            src="{{ $block['content']['src'] }}"
                                            alt="{{ $block['content']['alt'] ?? '' }}"
                                            class="h-8 w-12 object-cover rounded shrink-0"
                                            onerror="this.style.display='none'"
                                        />
                                        <span class="truncate text-zinc-600 dark:text-zinc-400">{{ $block['content']['alt'] ?: Str::limit($block['content']['src'], 40) }}</span>
                                    @elseif (isset($block['content']['text']))
                                        <span class="truncate text-zinc-600 dark:text-zinc-400">{{ Str::limit($block['content']['text'], 50) }}</span>
                                    @elseif (isset($block['content']['heading']))
                                        <span class="truncate text-zinc-600 dark:text-zinc-400">{{ Str::limit($block['content']['heading'], 50) }}</span>
                                    @elseif (isset($block['content']['title']))
                                        <span class="truncate text-zinc-600 dark:text-zinc-400">{{ Str::limit($block['content']['title'], 50) }}</span>
                                    @elseif (isset($block['content']['html']))
                                        <span class="truncate text-zinc-600 dark:text-zinc-400">{{ Str::limit(strip_tags($block['content']['html']), 50) }}</span>
                                    @elseif ($block['type'] === 'table' && isset($block['content']['headers']))
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ count($block['content']['headers']) }} {{ __('pages.create.columns') }}, {{ count($block['content']['rows'] ?? []) }} {{ __('pages.create.rows') }}</span>
                                    @elseif (in_array($block['type'], ['grid', 'columns']) && !empty($block['children']))
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ $block['content']['columns'] ?? 2 }} {{ __('pages.create.columns') }}, {{ count($block['children']) }} {{ __('pages.create.children') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </flux:card>

        {{-- Template selection --}}
        <flux:card :class="count($importedBlocks) > 0 ? 'opacity-50' : ''">
            <flux:heading size="lg" class="mb-4">{{ __('pages.create.template') }}</flux:heading>
            <flux:text class="mb-4">
                {{ __('pages.create.template_description') }}
                @if (count($importedBlocks) > 0)
                    <span class="text-amber-600 dark:text-amber-400">{{ __('pages.create.template_disabled_by_import') }}</span>
                @endif
            </flux:text>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                {{-- Blank option --}}
                <button
                    type="button"
                    wire:click="selectTemplate(null)"
                    @if (count($importedBlocks) > 0) disabled @endif
                    class="flex flex-col items-center p-4 rounded-lg border-2 transition-colors text-center {{ $templateId === null && count($importedBlocks) === 0 ? 'border-accent bg-accent/5' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent/50' }} {{ count($importedBlocks) > 0 ? 'cursor-not-allowed' : '' }}"
                >
                    <div class="w-full h-24 bg-zinc-100 dark:bg-zinc-700 rounded-lg mb-3 flex items-center justify-center">
                        <flux:icon name="document" class="size-8 text-zinc-400" />
                    </div>
                    <flux:heading size="sm">{{ __('pages.create.blank') }}</flux:heading>
                    <flux:text size="xs" class="text-zinc-500 mt-1">{{ __('pages.create.blank_description') }}</flux:text>
                </button>

                {{-- Templates --}}
                @foreach ($this->templates as $template)
                    <button
                        type="button"
                        wire:click="selectTemplate('{{ $template->id }}')"
                        @if (count($importedBlocks) > 0) disabled @endif
                        class="flex flex-col items-center p-4 rounded-lg border-2 transition-colors text-center {{ $templateId === $template->id ? 'border-accent bg-accent/5' : 'border-zinc-200 dark:border-zinc-600 hover:border-accent/50' }} {{ count($importedBlocks) > 0 ? 'cursor-not-allowed' : '' }}"
                    >
                        @if ($template->thumbnail_path)
                            <img src="{{ Storage::url($template->thumbnail_path) }}" alt="{{ $template->name }}" class="w-full h-24 object-cover rounded-lg mb-3" />
                        @else
                            <div class="w-full h-24 bg-zinc-100 dark:bg-zinc-700 rounded-lg mb-3 flex items-center justify-center">
                                <flux:icon :name="$template->category->icon()" class="size-8 text-zinc-400" />
                            </div>
                        @endif
                        <flux:heading size="sm">{{ $template->name }}</flux:heading>
                        <flux:text size="xs" class="text-zinc-500 mt-1">{{ $template->description }}</flux:text>
                    </button>
                @endforeach
            </div>
        </flux:card>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" href="{{ route('pages.index') }}">
                {{ __('pages.create.cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ __('pages.create.create_page') }}
            </flux:button>
        </div>
    </form>
</div>
