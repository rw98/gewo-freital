<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <flux:button
                href="{{ route('listing-requests.portal', $listingRequest->access_token) }}"
                variant="ghost"
                icon="arrow-left"
                size="sm"
                wire:navigate
            >
                {{ __('listing_requests.back_to_request') }}
            </flux:button>
        </div>

        <flux:card>
            <div class="mb-6">
                <flux:heading size="xl">{{ $listingRequest->customForm->name }}</flux:heading>
                @if ($listingRequest->customForm->description)
                    <flux:text class="mt-2 text-zinc-600">
                        {{ $listingRequest->customForm->description }}
                    </flux:text>
                @endif
                <flux:text size="sm" class="mt-2 text-zinc-500">
                    {{ __('forms.custom_form.for_listing', ['title' => $listingRequest->listing->title]) }}
                </flux:text>
            </div>

            @if ($listingRequest->hasCustomForm())
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    {{ __('forms.custom_form.already_completed') }}
                </flux:callout>
            @endif

            <livewire:forms.dynamic-form
                :form="$listingRequest->customForm"
                :listing-request="$listingRequest"
            />
        </flux:card>
    </div>
</div>
