<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <flux:card>
            <div class="mb-6">
                <flux:heading size="xl">{{ $form->name }}</flux:heading>
                @if ($form->description)
                    <flux:text class="mt-2 text-zinc-600">{{ $form->description }}</flux:text>
                @endif
            </div>

            <livewire:forms.dynamic-form :form="$form" />
        </flux:card>
    </div>
</div>
