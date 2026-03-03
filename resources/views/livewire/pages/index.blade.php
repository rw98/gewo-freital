<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('pages.index.title') }}</flux:heading>
            <flux:text class="mt-1">{{ __('pages.index.description') }}</flux:text>
        </div>
        <flux:button variant="primary" href="{{ route('pages.create') }}" icon="plus">
            {{ __('pages.index.create') }}
        </flux:button>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-wrap items-center gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('pages.index.search_placeholder')"
            class="w-64"
        />

        <flux:select wire:model.live="status" class="w-40">
            <flux:select.option value="">{{ __('pages.index.all_statuses') }}</flux:select.option>
            @foreach ($this->statuses as $statusOption)
                <flux:select.option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Pages table --}}
    <flux:card class="overflow-hidden">
        @if ($pages->isEmpty())
            <div class="p-12 text-center">
                <flux:icon name="document" class="size-12 text-zinc-300 mx-auto" />
                <flux:heading size="lg" class="mt-4">{{ __('pages.index.empty.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('pages.index.empty.description') }}</flux:text>
                <flux:button variant="primary" href="{{ route('pages.create') }}" class="mt-6" icon="plus">
                    {{ __('pages.index.empty.create') }}
                </flux:button>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('pages.index.columns.title') }}</flux:table.column>
                    <flux:table.column>{{ __('pages.index.columns.slug') }}</flux:table.column>
                    <flux:table.column>{{ __('pages.index.columns.status') }}</flux:table.column>
                    <flux:table.column>{{ __('pages.index.columns.creator') }}</flux:table.column>
                    <flux:table.column>{{ __('pages.index.columns.updated_at') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($pages as $page)
                        <flux:table.row wire:key="page-{{ $page->id }}">
                            <flux:table.cell>
                                <a href="{{ route('pages.builder', $page) }}" class="font-medium hover:text-accent">
                                    {{ $page->title }}
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm" class="text-zinc-500">/p/{{ $page->slug }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$page->status->color()" size="sm">
                                    {{ $page->status->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $page->creator?->name ?? '-' }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm" class="text-zinc-500">
                                    {{ $page->updated_at->diffForHumans() }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />

                                    <flux:menu>
                                        <flux:menu.item icon="pencil" href="{{ route('pages.builder', $page) }}">
                                            {{ __('pages.index.actions.edit') }}
                                        </flux:menu.item>
                                        @if ($page->isPublished())
                                            <flux:menu.item icon="eye" href="{{ route('pages.show', $page->slug) }}" target="_blank">
                                                {{ __('pages.index.actions.view') }}
                                            </flux:menu.item>
                                        @endif
                                        <flux:menu.item icon="document-duplicate" wire:click="duplicatePage('{{ $page->id }}')">
                                            {{ __('pages.index.actions.duplicate') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item
                                            icon="trash"
                                            variant="danger"
                                            wire:click="deletePage('{{ $page->id }}')"
                                            wire:confirm="{{ __('pages.index.confirm_delete') }}"
                                        >
                                            {{ __('pages.index.actions.delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $pages->links() }}
            </div>
        @endif
    </flux:card>
</div>
