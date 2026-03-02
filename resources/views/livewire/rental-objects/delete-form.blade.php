<div>
    <flux:modal.trigger name="confirm-rental-object-deletion-{{ $rentalObject->id }}">
        <flux:button variant="danger" icon="trash">
            {{ __('Delete') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-rental-object-deletion-{{ $rentalObject->id }}" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete this property?') }}</flux:heading>

                <flux:subheading class="mt-2">
                    {{ __('This will permanently delete the property at :address, including all flats, rooms, and associated data. This action cannot be undone.', ['address' => $rentalObject->fullAddress()]) }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete">{{ __('Delete Property') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
