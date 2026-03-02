<div class="space-y-6">
    {{-- Flash Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" dismissible>
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" dismissible>
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:button href="{{ route('listing-requests.index') }}" variant="ghost" size="sm" icon="arrow-left" class="mb-2" wire:navigate>
                {{ __('listing_requests.all_requests') }}
            </flux:button>
            <flux:heading size="xl">{{ $listingRequest->fullName() }}</flux:heading>
            <flux:text class="text-gewo-grey-500">{{ $listingRequest->email }} {{ $listingRequest->phone ? '· '.$listingRequest->phone : '' }}</flux:text>
        </div>
        <flux:badge size="lg" color="{{ $listingRequest->status->color() }}">
            {{ $listingRequest->status->label() }}
        </flux:badge>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Actions --}}
            @if (!$listingRequest->isTerminal())
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('listing_requests.change_status') }}</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->allowedTransitions as $status)
                            <flux:button
                                wire:click="openStatusModal('{{ $status->value }}')"
                                variant="{{ $status === \App\Enums\ListingRequestStatus::Rejected ? 'danger' : 'outline' }}"
                                size="sm"
                            >
                                {{ $status->label() }}
                            </flux:button>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            {{-- Initial Message --}}
            @if ($listingRequest->message)
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('listing_requests.message') }}</flux:heading>
                    <flux:text class="whitespace-pre-line">{{ $listingRequest->message }}</flux:text>
                </flux:card>
            @endif

            {{-- Documents --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">{{ __('listing_requests.documents') }}</flux:heading>
                    <flux:badge>{{ $listingRequest->documents->count() }}</flux:badge>
                </div>

                @if ($listingRequest->documents->isEmpty())
                    <flux:text class="text-gewo-grey-500">{{ __('listing_requests.no_documents') }}</flux:text>
                @else
                    <div class="space-y-2">
                        @foreach ($listingRequest->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-gewo-grey-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="{{ str_contains($document->mime_type, 'pdf') ? 'document-text' : 'photo' }}" class="size-5 text-gewo-grey-500" />
                                    <div>
                                        <flux:text size="sm" class="font-medium">{{ $document->original_filename }}</flux:text>
                                        <div class="flex items-center gap-2 text-xs text-gewo-grey-500">
                                            <flux:badge size="sm" variant="outline">{{ $document->type->label() }}</flux:badge>
                                            <span>{{ $document->humanReadableSize() }}</span>
                                            <span>·</span>
                                            <span>{{ $document->isUploadedByRequestee() ? 'Interessent' : $document->uploadedByUser?->first_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Messages --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('listing_requests.messages') }}</flux:heading>

                @if ($listingRequest->messages->isEmpty())
                    <flux:text class="text-gewo-grey-500 mb-4">{{ __('listing_requests.no_messages') }}</flux:text>
                @else
                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4">
                        @foreach ($listingRequest->messages->sortBy('created_at') as $message)
                            <div class="flex {{ $message->isSentByEmployee() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-sm {{ $message->isSentByEmployee() ? 'bg-accent text-white' : 'bg-gewo-grey-100' }} rounded-lg p-3">
                                    <div class="flex items-center gap-2 mb-1 text-xs {{ $message->isSentByEmployee() ? 'text-white/80' : 'text-gewo-grey-500' }}">
                                        @if ($message->isSentByEmployee() && $message->user)
                                            {{ $message->user->first_name }}
                                        @else
                                            {{ $listingRequest->first_name }}
                                        @endif
                                        · {{ $message->created_at->format('d.m. H:i') }}
                                    </div>
                                    <p class="text-sm whitespace-pre-line">{{ $message->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form wire:submit="sendMessage" class="pt-4 border-t border-gewo-grey-200">
                    <flux:field>
                        <flux:textarea wire:model="messageContent" rows="2" placeholder="{{ __('Write a message...') }}" />
                        <flux:error name="messageContent" />
                    </flux:field>
                    <div class="flex justify-end mt-2">
                        <flux:button type="submit" variant="primary" size="sm" icon="paper-airplane">
                            {{ __('listing_requests.send_message') }}
                        </flux:button>
                    </div>
                </form>
            </flux:card>

            {{-- Appointments --}}
            @if ($listingRequest->appointments->isNotEmpty())
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('listing_requests.appointments') }}</flux:heading>
                    <div class="space-y-2">
                        @foreach ($listingRequest->appointments as $appointment)
                            <div class="flex items-center justify-between p-3 bg-gewo-grey-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-{{ $appointment->status->color() }}-100 rounded-lg flex items-center justify-center">
                                        <flux:icon name="calendar" class="size-5 text-{{ $appointment->status->color() }}-600" />
                                    </div>
                                    <div>
                                        @if ($appointment->timeslot)
                                            <flux:text size="sm" class="font-medium">{{ $appointment->timeslot->starts_at->format('d.m.Y H:i') }}</flux:text>
                                            <flux:text size="sm" class="text-gewo-grey-500">{{ $appointment->timeslot->location ?? $listingRequest->listing->fullAddress() }}</flux:text>
                                        @else
                                            <flux:text size="sm" class="text-gewo-grey-500">{{ __('Timeslot deleted') }}</flux:text>
                                        @endif
                                    </div>
                                </div>
                                <flux:badge color="{{ $appointment->status->color() }}" size="sm">
                                    {{ $appointment->status->label() }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Listing Info --}}
            <flux:card>
                <flux:heading size="base" class="mb-3">{{ __('Listing') }}</flux:heading>
                <div class="aspect-video bg-gewo-grey-200 rounded-lg overflow-hidden mb-3">
                    @if ($listingRequest->listing->images->first())
                        <img src="{{ Storage::url($listingRequest->listing->images->first()->path) }}" alt="" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <flux:icon name="home" class="size-8 text-gewo-grey-400" />
                        </div>
                    @endif
                </div>
                <flux:heading size="sm">{{ $listingRequest->listing->title }}</flux:heading>
                <flux:text size="sm" class="text-gewo-grey-500">{{ $listingRequest->listing->fullAddress() }}</flux:text>
                <div class="mt-3">
                    <flux:button href="{{ route('listings.show', $listingRequest->listing) }}" variant="outline" size="sm" class="w-full" wire:navigate>
                        {{ __('View listing') }}
                    </flux:button>
                </div>
                <div class="mt-2">
                    <flux:button href="{{ route('listing-requests.timeslots', $listingRequest->listing) }}" variant="ghost" size="sm" class="w-full" icon="calendar-days" wire:navigate>
                        {{ __('listing_requests.manage_timeslots') }}
                    </flux:button>
                </div>
            </flux:card>

            {{-- Assignment --}}
            <flux:card>
                <flux:heading size="base" class="mb-3">{{ __('listing_requests.assign_to') }}</flux:heading>
                <flux:select wire:model.live="listingRequest.assigned_to" wire:change="assignTo($event.target.value)">
                    <flux:select.option value="">{{ __('listing_requests.not_assigned') }}</flux:select.option>
                    @foreach ($this->employees as $employee)
                        <flux:select.option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:card>

            {{-- Timeline --}}
            <flux:card>
                <flux:heading size="base" class="mb-3">{{ __('Timeline') }}</flux:heading>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <flux:icon name="paper-airplane" class="size-4 text-gewo-grey-400" />
                        <span>{{ __('listing_requests.requested_at') }}: {{ $listingRequest->requested_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @if ($listingRequest->email_confirmed_at)
                        <div class="flex items-center gap-2">
                            <flux:icon name="envelope-open" class="size-4 text-green-500" />
                            <span>{{ __('Email confirmed') }}: {{ $listingRequest->email_confirmed_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    @if ($listingRequest->approved_at)
                        <div class="flex items-center gap-2">
                            <flux:icon name="check-circle" class="size-4 text-green-500" />
                            <span>{{ __('Approved') }}: {{ $listingRequest->approved_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    @if ($listingRequest->signed_at)
                        <div class="flex items-center gap-2">
                            <flux:icon name="pencil" class="size-4 text-green-500" />
                            <span>{{ __('Signed') }}: {{ $listingRequest->signed_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    @if ($listingRequest->rejected_at)
                        <div class="flex items-center gap-2">
                            <flux:icon name="x-circle" class="size-4 text-red-500" />
                            <span>{{ __('Rejected') }}: {{ $listingRequest->rejected_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    @if ($listingRequest->closed_at)
                        <div class="flex items-center gap-2">
                            <flux:icon name="archive-box" class="size-4 text-gewo-grey-500" />
                            <span>{{ __('Closed') }}: {{ $listingRequest->closed_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>

    {{-- Status Transition Modal --}}
    <flux:modal wire:model="showStatusModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('listing_requests.confirm_transition') }}</flux:heading>
            @if ($newStatus)
                <flux:text>
                    {{ __('Status ändern zu') }}: <strong>{{ \App\Enums\ListingRequestStatus::tryFrom($newStatus)?->label() }}</strong>
                </flux:text>
            @endif
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showStatusModal', false)" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="transitionStatus" variant="primary">
                    {{ __('Confirm') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Reject Modal --}}
    <flux:modal wire:model="showRejectModal" class="max-w-md">
        <form wire:submit="reject" class="space-y-4">
            <flux:heading size="lg">{{ __('listing_requests.reject_request') }}</flux:heading>
            <flux:field>
                <flux:label>{{ __('listing_requests.rejection_reason') }}</flux:label>
                <flux:textarea wire:model="rejectionReason" rows="3" placeholder="{{ __('Optional reason for rejection...') }}" />
                <flux:error name="rejectionReason" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showRejectModal', false)" variant="ghost" type="button">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="danger">
                    {{ __('listing_requests.reject_request') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
