<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('rental-objects.show', $flat->rentalObject)" wire:navigate class="mb-2">
                {{ __('Back to Property') }}
            </flux:button>
            <flux:heading size="xl">{{ __('Flat') }} {{ $flat->number }}</flux:heading>
            <flux:text>{{ $flat->rentalObject->fullAddress() }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            @if ($this->canEdit)
                <flux:button variant="ghost" icon="pencil" :href="route('flats.edit', $flat)" wire:navigate>
                    {{ __('Edit') }}
                </flux:button>
            @endif
            @if ($this->canDelete)
                <livewire:flats.delete-form :flat="$flat" />
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <flux:card class="lg:col-span-2">
            <flux:heading size="lg" class="mb-4">{{ __('Flat Details') }}</flux:heading>

            <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Floor') }}</dt>
                    <dd class="mt-1">{{ $flat->floor }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Size') }}</dt>
                    <dd class="mt-1">{{ number_format($flat->size_sqm, 2) }} m²</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Accessible') }}</dt>
                    <dd class="mt-1">
                        @if ($flat->is_wheelchair_accessible)
                            <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Cold Rent') }}</dt>
                    <dd class="mt-1">{{ number_format($flat->rent_cold, 2) }} €</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Utilities') }}</dt>
                    <dd class="mt-1">{{ number_format($flat->utility_cost, 2) }} €</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Rent') }}</dt>
                    <dd class="mt-1 font-semibold">{{ number_format($flat->totalRent(), 2) }} €</dd>
                </div>
            </dl>

            @if ($flat->description)
                <div class="mt-6">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Description') }}</dt>
                    <dd class="mt-1 whitespace-pre-wrap">{{ $flat->description }}</dd>
                </div>
            @endif
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Tenants') }}</flux:heading>

            @if ($this->tenants->isEmpty())
                <flux:text>{{ __('No tenants assigned.') }}</flux:text>
            @else
                <ul class="space-y-3">
                    @foreach ($this->tenants as $tenant)
                        <li class="flex items-center gap-3">
                            <flux:avatar :name="$tenant->name" :initials="$tenant->initials()" size="sm" />
                            <div>
                                <div class="font-medium">{{ $tenant->name }}</div>
                                <flux:text size="sm">{{ $tenant->email }}</flux:text>
                                @if ($tenant->pivot->move_in_date)
                                    <flux:text size="sm">
                                        {{ __('Since') }} {{ \Carbon\Carbon::parse($tenant->pivot->move_in_date)->format('M Y') }}
                                    </flux:text>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

    @if ($this->rooms->isNotEmpty())
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Rooms') }}</flux:heading>

            <div class="flex flex-wrap gap-2">
                @foreach ($this->rooms as $room)
                    <flux:badge size="sm">{{ $room->name }}</flux:badge>
                @endforeach
            </div>
        </flux:card>
    @endif

    @if ($this->canEdit)
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Attributes') }}</flux:heading>
                <livewire:attribute-manager :model="$flat" />
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Images') }}</flux:heading>
                <livewire:image-upload :model="$flat" />
            </flux:card>
        </div>
    @endif

    @if ($this->notes->isNotEmpty())
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Notes') }}</flux:heading>

            <ul class="space-y-4">
                @foreach ($this->notes as $note)
                    <li class="border-b border-zinc-200 pb-4 last:border-0 last:pb-0 dark:border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            @if ($note->user)
                                <flux:avatar :name="$note->user->name" :initials="$note->user->initials()" size="xs" />
                                <flux:text size="sm" class="font-medium">{{ $note->user->name }}</flux:text>
                            @else
                                <flux:text size="sm" class="font-medium">{{ __('Anonymous') }}</flux:text>
                            @endif
                            <flux:text size="sm" class="text-zinc-500">{{ $note->created_at->diffForHumans() }}</flux:text>
                        </div>
                        <div class="whitespace-pre-wrap">{{ $note->content }}</div>
                    </li>
                @endforeach
            </ul>
        </flux:card>
    @endif
</div>
