@props(['block'])

<div class="space-y-4">
    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.recipient_email') }}</flux:label>
        <flux:input
            type="email"
            wire:model.live.debounce.500ms="editingContent.recipient_email"
            :placeholder="__('pages.blocks.contact_form.recipient_email_placeholder')"
        />
        <flux:description>{{ __('pages.blocks.contact_form.recipient_email_description') }}</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>{{ __('pages.blocks.contact_form.success_message') }}</flux:label>
        <flux:textarea
            wire:model.live.debounce.500ms="editingContent.success_message"
            :placeholder="__('pages.blocks.contact_form.success_message_placeholder')"
            rows="2"
        />
    </flux:field>
</div>
