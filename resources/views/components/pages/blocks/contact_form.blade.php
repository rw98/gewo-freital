@props(['block', 'preview' => false])

@php
    $recipientEmail = $block->getContent('recipient_email', '');
    $successMessage = $block->getContent('success_message', __('pages.blocks.contact_form.default_success'));
@endphp

<form class="space-y-4" @if($preview) onsubmit="event.preventDefault()" @else action="{{ route('pages.contact-form', $block->id) }}" method="POST" @endif>
    @csrf

    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.name') }}</flux:label>
        <flux:input name="name" required />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.email') }}</flux:label>
        <flux:input type="email" name="email" required />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.subject') }}</flux:label>
        <flux:input name="subject" required />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.message') }}</flux:label>
        <flux:textarea name="message" rows="4" required />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full">
        {{ __('pages.blocks.contact_form.send') }}
    </flux:button>
</form>
