<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Enums\EmploymentStatus;
use App\Models\ListingRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class SelfDisclosure extends Component
{
    public ListingRequest $listingRequest;

    // Tenants (paying and non-paying)
    public array $tenants = [];

    // Household
    public ?bool $has_pets = null;

    public ?string $pets_details = null;

    public ?bool $is_smoker = null;

    // Current tenancy
    public ?string $current_landlord_name = null;

    public ?string $current_landlord_phone = null;

    public ?string $current_landlord_email = null;

    public ?string $reason_for_moving = null;

    public ?string $desired_move_in_date = null;

    // Financial
    public ?bool $has_insolvency = null;

    public ?bool $has_eviction_history = null;

    public ?bool $has_rental_debt = null;

    public ?string $additional_notes = null;

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'tenants'])
            ->firstOrFail();

        if (! $this->listingRequest->canFillSelfDisclosure()) {
            abort(403, __('listing_requests.self_disclosure.not_available'));
        }

        // Fill with existing data
        $this->has_pets = $this->listingRequest->has_pets;
        $this->pets_details = $this->listingRequest->pets_details;
        $this->is_smoker = $this->listingRequest->is_smoker;
        $this->current_landlord_name = $this->listingRequest->current_landlord_name;
        $this->current_landlord_phone = $this->listingRequest->current_landlord_phone;
        $this->current_landlord_email = $this->listingRequest->current_landlord_email;
        $this->reason_for_moving = $this->listingRequest->reason_for_moving;
        $this->desired_move_in_date = $this->listingRequest->desired_move_in_date?->format('Y-m-d');
        $this->has_insolvency = $this->listingRequest->has_insolvency;
        $this->has_eviction_history = $this->listingRequest->has_eviction_history;
        $this->has_rental_debt = $this->listingRequest->has_rental_debt;
        $this->additional_notes = $this->listingRequest->additional_notes;

        // Load existing tenants
        if ($this->listingRequest->tenants->isNotEmpty()) {
            $this->tenants = $this->listingRequest->tenants->map(fn ($tenant) => [
                'id' => $tenant->id,
                'pays_rent' => $tenant->pays_rent,
                'first_name' => $tenant->first_name,
                'last_name' => $tenant->last_name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'date_of_birth' => $tenant->date_of_birth?->format('Y-m-d'),
                'relationship' => $tenant->relationship,
                'employment_status' => $tenant->employment_status?->value,
                'monthly_net_income' => $tenant->monthly_net_income,
            ])->toArray();
        } else {
            // Create first tenant pre-filled from the request
            $this->tenants[] = [
                'id' => null,
                'pays_rent' => true,
                'first_name' => $this->listingRequest->first_name,
                'last_name' => $this->listingRequest->last_name,
                'email' => $this->listingRequest->email,
                'phone' => $this->listingRequest->phone,
                'date_of_birth' => null,
                'relationship' => null,
                'employment_status' => null,
                'monthly_net_income' => null,
            ];
        }
    }

    public function getTitle(): string
    {
        return __('listing_requests.self_disclosure.title').' - '.$this->listingRequest->listing->title;
    }

    #[Computed]
    public function employmentStatuses(): array
    {
        return EmploymentStatus::cases();
    }

    public function addTenant(bool $paysRent = true): void
    {
        $this->tenants[] = [
            'id' => null,
            'pays_rent' => $paysRent,
            'first_name' => '',
            'last_name' => '',
            'email' => null,
            'phone' => null,
            'date_of_birth' => null,
            'relationship' => null,
            'employment_status' => null,
            'monthly_net_income' => null,
        ];
    }

    public function removeTenant(int $index): void
    {
        // Don't allow removing the last tenant
        if (count($this->tenants) <= 1) {
            return;
        }

        unset($this->tenants[$index]);
        $this->tenants = array_values($this->tenants);
    }

    public function rules(): array
    {
        $rules = [
            'tenants' => ['required', 'array', 'min:1'],
            'tenants.*.pays_rent' => ['required', 'boolean'],
            'tenants.*.first_name' => ['required', 'string', 'max:100'],
            'tenants.*.last_name' => ['required', 'string', 'max:100'],
            'tenants.*.date_of_birth' => ['required', 'date', 'before:today'],
            'tenants.*.relationship' => ['nullable', 'string', 'max:100'],
            'has_pets' => ['required', 'boolean'],
            'pets_details' => ['nullable', 'required_if:has_pets,true', 'string', 'max:500'],
            'is_smoker' => ['required', 'boolean'],
            'current_landlord_name' => ['nullable', 'string', 'max:255'],
            'current_landlord_phone' => ['nullable', 'string', 'max:50'],
            'current_landlord_email' => ['nullable', 'email', 'max:255'],
            'reason_for_moving' => ['required', 'string', 'max:1000'],
            'desired_move_in_date' => ['required', 'date', 'after_or_equal:today'],
            'has_insolvency' => ['required', 'boolean'],
            'has_eviction_history' => ['required', 'boolean'],
            'has_rental_debt' => ['required', 'boolean'],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
        ];

        // Add conditional rules for tenants based on pays_rent
        foreach ($this->tenants as $index => $tenant) {
            if ($tenant['pays_rent'] ?? true) {
                // Paying tenants need full info including contact
                $rules["tenants.{$index}.email"] = ['required', 'email', 'max:255'];
                $rules["tenants.{$index}.phone"] = ['nullable', 'string', 'max:50'];
                $rules["tenants.{$index}.employment_status"] = ['required', 'string'];
                $rules["tenants.{$index}.monthly_net_income"] = ['required', 'numeric', 'min:0'];
            } else {
                // Non-paying occupants only need basic info
                $rules["tenants.{$index}.email"] = ['nullable', 'email', 'max:255'];
                $rules["tenants.{$index}.phone"] = ['nullable', 'string', 'max:50'];
                $rules["tenants.{$index}.employment_status"] = ['nullable', 'string'];
                $rules["tenants.{$index}.monthly_net_income"] = ['nullable', 'numeric', 'min:0'];
            }
        }

        return $rules;
    }

    public function save(): void
    {
        if (! $this->listingRequest->canFillSelfDisclosure()) {
            abort(403);
        }

        $validated = $this->validate();

        // Update the listing request
        $this->listingRequest->update([
            'has_pets' => $validated['has_pets'],
            'pets_details' => $validated['pets_details'],
            'is_smoker' => $validated['is_smoker'],
            'current_landlord_name' => $validated['current_landlord_name'],
            'current_landlord_phone' => $validated['current_landlord_phone'],
            'current_landlord_email' => $validated['current_landlord_email'],
            'reason_for_moving' => $validated['reason_for_moving'],
            'desired_move_in_date' => $validated['desired_move_in_date'],
            'has_insolvency' => $validated['has_insolvency'],
            'has_eviction_history' => $validated['has_eviction_history'],
            'has_rental_debt' => $validated['has_rental_debt'],
            'additional_notes' => $validated['additional_notes'],
            'self_disclosure_completed_at' => now(),
        ]);

        // Sync tenants
        $existingTenantIds = $this->listingRequest->tenants->pluck('id')->toArray();
        $updatedTenantIds = [];

        foreach ($validated['tenants'] as $tenantData) {
            $isPaying = $tenantData['pays_rent'] ?? true;

            $tenantFields = [
                'pays_rent' => $isPaying,
                'first_name' => $tenantData['first_name'],
                'last_name' => $tenantData['last_name'],
                'email' => $isPaying ? $tenantData['email'] : null,
                'phone' => $tenantData['phone'] ?? null,
                'date_of_birth' => $tenantData['date_of_birth'],
                'relationship' => $tenantData['relationship'],
                'employment_status' => $isPaying ? $tenantData['employment_status'] : null,
                'monthly_net_income' => $isPaying ? $tenantData['monthly_net_income'] : null,
            ];

            if (! empty($tenantData['id'])) {
                // Update existing tenant
                $this->listingRequest->tenants()
                    ->where('id', $tenantData['id'])
                    ->update($tenantFields);
                $updatedTenantIds[] = $tenantData['id'];
            } else {
                // Create new tenant
                $tenant = $this->listingRequest->tenants()->create($tenantFields);
                $updatedTenantIds[] = $tenant->id;
            }
        }

        // Delete removed tenants
        $tenantsToDelete = array_diff($existingTenantIds, $updatedTenantIds);
        if (! empty($tenantsToDelete)) {
            $this->listingRequest->tenants()->whereIn('id', $tenantsToDelete)->delete();
        }

        session()->flash('success', __('listing_requests.self_disclosure.saved'));

        $this->redirect(
            route('listing-requests.portal', $this->listingRequest->access_token),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.self-disclosure')
            ->title($this->getTitle());
    }
}
