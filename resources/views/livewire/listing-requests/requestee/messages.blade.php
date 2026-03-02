<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listing-requests.portal', $listingRequest->access_token) }}">{{ __('listing_requests.portal_title') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('listing_requests.messages') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="success" icon="check-circle" dismissible>
                {{ session('success') }}
            </flux:callout>
        </div>
    @endif

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Messages --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('listing_requests.messages') }}</flux:heading>

                @if ($this->conversationMessages->isEmpty())
                    <div class="text-center py-8">
                        <flux:icon name="chat-bubble-left-right" class="size-12 mx-auto text-gewo-grey-300 mb-3" />
                        <flux:text class="text-gewo-grey-500">{{ __('listing_requests.no_messages') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach ($this->conversationMessages as $message)
                            <div class="flex {{ $message->isSentByRequestee() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs sm:max-w-md {{ $message->isSentByRequestee() ? 'bg-accent text-white' : 'bg-gewo-grey-100 text-gewo-grey-800' }} rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if ($message->isSentByEmployee() && $message->user)
                                            <flux:avatar size="xs" name="{{ $message->user->first_name }} {{ $message->user->last_name }}" />
                                            <span class="text-sm font-medium {{ $message->isSentByRequestee() ? 'text-white/90' : 'text-gewo-grey-600' }}">
                                                {{ $message->user->first_name }} {{ $message->user->last_name }}
                                            </span>
                                        @else
                                            <span class="text-sm font-medium {{ $message->isSentByRequestee() ? 'text-white/90' : 'text-gewo-grey-600' }}">
                                                {{ $message->isSentByRequestee() ? __('listing_requests.sent_by_you') : __('listing_requests.sent_by_employee') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="whitespace-pre-line text-sm">{{ $message->content }}</p>
                                    <div class="mt-2 text-xs {{ $message->isSentByRequestee() ? 'text-white/70' : 'text-gewo-grey-500' }}">
                                        {{ $message->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- New Message Form --}}
                <form wire:submit="send" class="mt-6 pt-6 border-t border-gewo-grey-200">
                    <flux:field>
                        <flux:label>{{ __('listing_requests.new_message') }}</flux:label>
                        <flux:textarea
                            wire:model="content"
                            rows="3"
                            placeholder="{{ __('listing_requests.message_content') }}..."
                        />
                        <flux:error name="content" />
                    </flux:field>
                    <div class="flex justify-end mt-3">
                        <flux:button type="submit" variant="primary" icon="paper-airplane">
                            {{ __('listing_requests.send_message') }}
                        </flux:button>
                    </div>
                </form>
            </flux:card>

            {{-- Back Button --}}
            <div class="flex justify-start">
                <flux:button href="{{ route('listing-requests.portal', $listingRequest->access_token) }}" variant="ghost" icon="arrow-left" wire:navigate>
                    {{ __('Back to overview') }}
                </flux:button>
            </div>
        </div>
    </section>
</div>
