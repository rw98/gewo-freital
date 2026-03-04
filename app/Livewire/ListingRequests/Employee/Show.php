<?php

namespace App\Livewire\ListingRequests\Employee;

use App\Enums\ListingRequestStatus;
use App\Models\Form;
use App\Models\ListingRequest;
use App\Models\RequestMessage;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Services\ListingRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public ListingRequest $listingRequest;

    #[Validate('required|string|max:2000')]
    public string $messageContent = '';

    #[Validate('nullable|string|max:500')]
    public string $rejectionReason = '';

    public string $newStatus = '';

    public bool $showStatusModal = false;

    public bool $showRejectModal = false;

    public bool $showPrefillModal = false;

    public array $prefillValues = [];

    public array $lockedFields = [];

    public function mount(ListingRequest $listingRequest): void
    {
        Gate::authorize('view', $listingRequest);

        $this->listingRequest = $listingRequest->load([
            'listing',
            'assignedTo',
            'approvedBy',
            'documents.uploadedByUser',
            'messages.user',
            'appointments.timeslot',
        ]);

        // Mark requestee messages as read
        $this->listingRequest->messages()
            ->where('sender_type', 'requestee')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getTitle(): string
    {
        return __('listing_requests.request').' - '.$this->listingRequest->fullName();
    }

    #[Computed]
    public function allowedTransitions(): array
    {
        return $this->listingRequest->status->allowedTransitions();
    }

    #[Computed]
    public function employees()
    {
        return User::query()->orderBy('first_name')->get();
    }

    #[Computed]
    public function availableForms()
    {
        return Form::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function openStatusModal(string $status): void
    {
        if ($status === ListingRequestStatus::Rejected->value) {
            $this->showRejectModal = true;
        } else {
            $this->newStatus = $status;
            $this->showStatusModal = true;
        }
    }

    public function transitionStatus(): void
    {
        Gate::authorize('transitionStatus', $this->listingRequest);

        $status = ListingRequestStatus::from($this->newStatus);

        if (! $this->listingRequest->canTransitionTo($status)) {
            session()->flash('error', __('listing_requests.transition_not_allowed'));

            return;
        }

        app(ListingRequestService::class)->transitionStatus(
            $this->listingRequest,
            $status,
            auth()->user()
        );

        $this->showStatusModal = false;
        $this->newStatus = '';
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.status_changed'));
    }

    public function reject(): void
    {
        $this->validate(['rejectionReason' => 'nullable|string|max:500']);

        Gate::authorize('transitionStatus', $this->listingRequest);

        app(ListingRequestService::class)->transitionStatus(
            $this->listingRequest,
            ListingRequestStatus::Rejected,
            auth()->user(),
            $this->rejectionReason
        );

        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.status_changed'));
    }

    public function assignTo(string $userId): void
    {
        Gate::authorize('update', $this->listingRequest);

        $this->listingRequest->update([
            'assigned_to' => $userId ?: null,
        ]);

        $this->listingRequest->refresh();
    }

    public function assignForm(string $formId): void
    {
        Gate::authorize('update', $this->listingRequest);

        $this->listingRequest->update([
            'custom_form_id' => $formId ?: null,
            'custom_form_completed_at' => null, // Reset completion when changing form
            'form_prefilled_values' => null,
            'form_locked_fields' => null,
        ]);

        $this->listingRequest->refresh();
    }

    public function openPrefillModal(): void
    {
        if (! $this->listingRequest->customForm) {
            return;
        }

        // Load existing prefilled values
        $this->prefillValues = $this->listingRequest->form_prefilled_values ?? [];
        $this->lockedFields = $this->listingRequest->form_locked_fields ?? [];

        $this->showPrefillModal = true;
    }

    public function savePrefillValues(): void
    {
        Gate::authorize('update', $this->listingRequest);

        // Filter out empty values
        $values = array_filter($this->prefillValues, fn ($v) => $v !== '' && $v !== null);

        $this->listingRequest->update([
            'form_prefilled_values' => ! empty($values) ? $values : null,
            'form_locked_fields' => ! empty($this->lockedFields) ? $this->lockedFields : null,
        ]);

        $this->showPrefillModal = false;
        $this->listingRequest->refresh();

        session()->flash('success', __('forms.prefill.saved'));
    }

    public function toggleFieldLock(string $fieldName): void
    {
        if (in_array($fieldName, $this->lockedFields)) {
            $this->lockedFields = array_values(array_diff($this->lockedFields, [$fieldName]));
        } else {
            $this->lockedFields[] = $fieldName;
        }
    }

    public function sendMessage(): void
    {
        $this->validate(['messageContent' => 'required|string|max:2000']);

        Gate::authorize('sendMessage', $this->listingRequest);

        $message = RequestMessage::create([
            'listing_request_id' => $this->listingRequest->id,
            'user_id' => auth()->id(),
            'sender_type' => 'employee',
            'content' => $this->messageContent,
        ]);

        // Notify the requestee
        Notification::route('mail', $this->listingRequest->email)
            ->notify(new NewMessageNotification($this->listingRequest, $message));

        $this->reset('messageContent');
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.message_sent'));
    }

    public function render(): View
    {
        return view('livewire.listing-requests.employee.show')
            ->title($this->getTitle());
    }
}
