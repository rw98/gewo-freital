@props(['block', 'preview' => false])

@if ($preview)
    {{-- Preview mode: show static form --}}
    <form class="space-y-4" onsubmit="event.preventDefault()">
        <flux:field>
            <flux:label>{{ __('pages.blocks.contact_form.name') }}</flux:label>
            <flux:input disabled />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('pages.blocks.contact_form.email') }}</flux:label>
            <flux:input type="email" disabled />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('pages.blocks.contact_form.subject') }}</flux:label>
            <flux:input disabled />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('pages.blocks.contact_form.message') }}</flux:label>
            <flux:textarea rows="4" disabled />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full" disabled>
            {{ __('pages.blocks.contact_form.send') }}
        </flux:button>
    </form>
@else
    {{-- Public mode: use Livewire component --}}
    <livewire:pages.contact-form :block="$block" />
@endif
