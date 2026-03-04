<div>
    @if ($submitted)
        <flux:callout icon="check-circle" color="green">
            <flux:callout.text>
                {{ $block->getContent('success_message', __('pages.blocks.contact_form.default_success')) }}
            </flux:callout.text>
        </flux:callout>
    @else
        <form wire:submit="submit" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('pages.blocks.contact_form.name') }}</flux:label>
                <flux:input wire:model="name" required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('pages.blocks.contact_form.email') }}</flux:label>
                <flux:input type="email" wire:model="email" required />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('pages.blocks.contact_form.subject') }}</flux:label>
                <flux:input wire:model="subject" required />
                <flux:error name="subject" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('pages.blocks.contact_form.message') }}</flux:label>
                <flux:textarea wire:model="message" rows="4" required />
                <flux:error name="message" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('pages.blocks.contact_form.send') }}
            </flux:button>
        </form>
    @endif
</div>
