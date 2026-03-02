<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('rental-objects.index')" wire:navigate class="mb-2">
                {{ __('Back to Properties') }}
            </flux:button>
            <flux:heading size="xl">{{ $rentalObject->street }} {{ $rentalObject->number }}</flux:heading>
            <flux:text>{{ $rentalObject->postal_code }} {{ $rentalObject->city }}, {{ $rentalObject->country }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            @if ($this->canEdit)
                <flux:button variant="ghost" icon="pencil" :href="route('rental-objects.edit', $rentalObject)" wire:navigate>
                    {{ __('Edit') }}
                </flux:button>
            @endif
            @if ($this->canDelete)
                <livewire:rental-objects.delete-form :rental-object="$rentalObject" />
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <flux:card class="lg:col-span-2">
            <flux:heading size="lg" class="mb-4">{{ __('Property Details') }}</flux:heading>

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Object Number') }}</dt>
                    <dd class="mt-1 font-mono">{{ $rentalObject->object_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Address') }}</dt>
                    <dd class="mt-1">{{ $rentalObject->fullAddress() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Elevator') }}</dt>
                    <dd class="mt-1">
                        @if ($rentalObject->has_elevator)
                            <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Year Built') }}</dt>
                    <dd class="mt-1">{{ $rentalObject->year_built ?? __('Unknown') }}</dd>
                </div>
            </dl>
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Contacts') }}</flux:heading>

            @if ($this->contacts->isEmpty())
                <flux:text>{{ __('No contacts assigned.') }}</flux:text>
            @else
                <ul class="space-y-3">
                    @foreach ($this->contacts as $contact)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:avatar :name="$contact->name" :initials="$contact->initials()" size="sm" />
                                <div>
                                    <div class="font-medium">{{ $contact->name }}</div>
                                    <flux:text size="sm">{{ $contact->email }}</flux:text>
                                </div>
                            </div>
                            <flux:badge size="sm">{{ ucfirst($contact->pivot->role) }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

    @if ($this->canEdit)
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Attributes') }}</flux:heading>
                <livewire:attribute-manager :model="$rentalObject" />
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Images') }}</flux:heading>
                <livewire:image-upload :model="$rentalObject" />
            </flux:card>
        </div>
    @endif

    <div>
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">{{ __('Flats') }}</flux:heading>
            @if ($this->canCreateFlat)
                <flux:button variant="primary" icon="plus" :href="route('flats.create', $rentalObject)" wire:navigate>
                    {{ __('Add Flat') }}
                </flux:button>
            @endif
        </div>

        @if ($this->flats->isEmpty())
            <flux:card class="text-center py-8">
                <flux:icon name="home" class="mx-auto size-10 text-zinc-400" />
                <flux:heading class="mt-3">{{ __('No flats yet') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Add flats to this property to get started.') }}</flux:text>
                @if ($this->canCreateFlat)
                    <flux:button variant="primary" icon="plus" :href="route('flats.create', $rentalObject)" wire:navigate class="mt-4">
                        {{ __('Add Flat') }}
                    </flux:button>
                @endif
            </flux:card>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Number') }}</flux:table.column>
                    <flux:table.column>{{ __('Floor') }}</flux:table.column>
                    <flux:table.column>{{ __('Size') }}</flux:table.column>
                    <flux:table.column>{{ __('Rent') }}</flux:table.column>
                    <flux:table.column>{{ __('Accessible') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->flats as $flat)
                        <flux:table.row wire:key="flat-{{ $flat->id }}">
                            <flux:table.cell variant="strong">{{ $flat->number }}</flux:table.cell>
                            <flux:table.cell>{{ $flat->floor }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($flat->size_sqm, 2) }} m²</flux:table.cell>
                            <flux:table.cell>{{ number_format($flat->totalRent(), 2) }} €</flux:table.cell>
                            <flux:table.cell>
                                @if ($flat->is_wheelchair_accessible)
                                    <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="eye" :href="route('flats.show', $flat)" wire:navigate />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>
</div>
