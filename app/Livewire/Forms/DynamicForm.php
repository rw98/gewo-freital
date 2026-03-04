<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormResponse;
use App\Models\ListingRequest;
use App\Services\FormAutoFillService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class DynamicForm extends Component
{
    use WithFileUploads;

    public Form $form;

    public ?ListingRequest $listingRequest = null;

    public ?string $autoFillSourceType = null;

    public ?string $autoFillSourceId = null;

    public array $values = [];

    public array $files = [];

    public bool $submitted = false;

    public function mount(
        Form $form,
        ?ListingRequest $listingRequest = null,
        ?string $autoFillSourceType = null,
        ?string $autoFillSourceId = null
    ): void {
        $this->form = $form->load('fields');
        $this->listingRequest = $listingRequest;
        $this->autoFillSourceType = $autoFillSourceType;
        $this->autoFillSourceId = $autoFillSourceId;

        // Initialize values with defaults (skip info fields)
        foreach ($this->form->fields as $field) {
            if ($field->type === FormFieldType::Info) {
                continue;
            }

            if ($field->type === FormFieldType::Checkbox) {
                $this->values[$field->name] = $field->getConfig('default_checked', false);
            } else {
                $this->values[$field->name] = '';
            }
        }

        // Apply auto-fill values
        $this->applyAutoFill();
    }

    /**
     * Apply auto-fill values from source models.
     */
    private function applyAutoFill(): void
    {
        // Collect all unique source types from field configs
        $sourceModels = [];

        foreach ($this->form->fields as $field) {
            $source = $field->getConfig('autofill_source');
            $sourceField = $field->getConfig('autofill_field');

            if (! $source || ! $sourceField) {
                continue;
            }

            // Resolve the source model (cache per source type)
            if (! isset($sourceModels[$source])) {
                $sourceModels[$source] = $this->resolveAutoFillSource($source);
            }

            $model = $sourceModels[$source];
            if (! $model) {
                continue;
            }

            // Get value from model
            $value = FormAutoFillService::getValueFromModel($model, $sourceField);

            if ($value !== null) {
                $this->values[$field->name] = $this->formatAutoFillValue($value, $field->type);
            }
        }
    }

    /**
     * Resolve the auto-fill source model.
     */
    private function resolveAutoFillSource(string $sourceType): ?Model
    {
        // If explicit source is provided via mount params
        if ($this->autoFillSourceType === $sourceType && $this->autoFillSourceId) {
            return FormAutoFillService::resolveSource($sourceType, $this->autoFillSourceId);
        }

        // Auto-resolve from listing request
        if ($this->listingRequest) {
            return match ($sourceType) {
                'listing_request' => $this->listingRequest,
                'listing' => $this->listingRequest->listing,
                'flat' => $this->listingRequest->listing?->flat,
                default => null,
            };
        }

        return null;
    }

    /**
     * Format auto-fill value for the field type.
     */
    private function formatAutoFillValue(mixed $value, FormFieldType $fieldType): mixed
    {
        if ($value === null) {
            return '';
        }

        return match ($fieldType) {
            FormFieldType::Checkbox => (bool) $value,
            FormFieldType::Number => is_numeric($value) ? $value : 0,
            FormFieldType::Date => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value,
            default => (string) $value,
        };
    }

    #[Computed]
    public function fields()
    {
        return $this->form->fields;
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->form->fields as $field) {
            // Skip info fields - they don't have inputs
            if ($field->type === FormFieldType::Info) {
                continue;
            }

            $fieldRules = $field->getValidationRules();

            // Handle file uploads separately
            if ($field->type === FormFieldType::File) {
                $rules['files.'.$field->name] = $fieldRules;
            } else {
                $rules['values.'.$field->name] = $fieldRules;
            }
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->form->fields as $field) {
            if ($field->type === FormFieldType::Info) {
                continue;
            }

            $attributes['values.'.$field->name] = $field->label;
            $attributes['files.'.$field->name] = $field->label;
        }

        return $attributes;
    }

    public function submit(): void
    {
        $this->validate();

        // Create the response
        $response = FormResponse::create([
            'form_id' => $this->form->id,
            'listing_request_id' => $this->listingRequest?->id,
            'submitter_email' => $this->values['email'] ?? null,
            'submitter_name' => $this->values['name'] ?? $this->values['first_name'] ?? null,
            'ip_address' => request()->ip(),
            'submitted_at' => now(),
        ]);

        // Create field values (skip info fields)
        foreach ($this->form->fields as $field) {
            if ($field->type === FormFieldType::Info) {
                continue;
            }

            $value = null;
            $filePath = null;
            $fileName = null;

            if ($field->type === FormFieldType::File && isset($this->files[$field->name])) {
                $file = $this->files[$field->name];
                $filePath = $file->store('form-uploads/'.$this->form->id, 'public');
                $fileName = $file->getClientOriginalName();
            } else {
                $value = $this->values[$field->name] ?? null;

                // Convert arrays to JSON for multi-select
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                // Convert booleans to string
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                }
            }

            $response->fieldValues()->create([
                'form_field_id' => $field->id,
                'value' => $value,
                'file_path' => $filePath,
                'file_name' => $fileName,
            ]);
        }

        // Update listing request if linked
        if ($this->listingRequest) {
            $this->listingRequest->update([
                'custom_form_completed_at' => now(),
            ]);
        }

        $this->submitted = true;
        $this->dispatch('form-submitted');
    }

    public function render(): View
    {
        return view('livewire.forms.dynamic-form');
    }
}
