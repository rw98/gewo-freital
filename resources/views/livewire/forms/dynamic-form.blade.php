<div>
    @if ($submitted)
        <div class="text-center py-12">
            <flux:icon name="check-circle" class="size-16 text-green-500 mx-auto" />
            <flux:heading size="lg" class="mt-4">{{ __('forms.dynamic.success_title') }}</flux:heading>
            <flux:text class="mt-2">
                {{ $form->success_message ?? __('forms.dynamic.success_message') }}
            </flux:text>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6">
            @if ($form->description)
                <flux:text class="text-zinc-600">{{ $form->description }}</flux:text>
            @endif

            @foreach ($this->fields as $field)
                <x-forms.field-renderer
                    :field="$field"
                    :name="$field->name"
                    wire:model="{{ $field->type->value === 'file' ? 'files.'.$field->name : 'values.'.$field->name }}"
                />
            @endforeach

            <div class="pt-4">
                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('forms.dynamic.submit') }}</span>
                    <span wire:loading>{{ __('forms.dynamic.submitting') }}</span>
                </flux:button>
            </div>
        </form>
    @endif
</div>
