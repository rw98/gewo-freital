<div>
    <flux:modal.trigger name="confirm-flat-deletion-{{ $flat->id }}">
        <flux:button variant="danger" icon="trash">
            {{ __('Delete') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-flat-deletion-{{ $flat->id }}" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete this flat?') }}</flux:heading>

                <flux:subheading class="mt-2">
                    {{ __('This will permanently delete flat :number, including all rooms, notes, and associated data. This action cannot be undone.', ['number' => $flat->number]) }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete">{{ __('Delete Flat') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
