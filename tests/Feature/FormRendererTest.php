<?php

use App\Enums\FormFieldType;
use App\Enums\ListingRequestStatus;
use App\Livewire\Forms\DynamicForm;
use App\Livewire\Forms\PublicForm;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormResponse;
use App\Models\Listing;
use App\Models\ListingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

describe('public form', function () {
    it('can access public form by key', function () {
        $form = Form::factory()->create(['is_active' => true]);

        $this->get(route('forms.public', $form->key))
            ->assertOk();
    });

    it('returns 404 for inactive form', function () {
        $form = Form::factory()->inactive()->create();

        $this->get(route('forms.public', $form->key))
            ->assertNotFound();
    });

    it('returns 404 for invalid key', function () {
        $this->get(route('forms.public', 'invalid-key'))
            ->assertNotFound();
    });

    it('displays form name and description', function () {
        $form = Form::factory()->create([
            'name' => 'Contact Form',
            'description' => 'Please fill out this form',
            'is_active' => true,
        ]);

        Livewire::test(PublicForm::class, ['key' => $form->key])
            ->assertSee('Contact Form')
            ->assertSee('Please fill out this form');
    });
});

describe('dynamic form rendering', function () {
    it('renders text field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'label' => 'Your Name',
            'placeholder' => 'Enter your name',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Your Name');
    });

    it('renders email field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'label' => 'Email Address',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Email Address');
    });

    it('renders textarea field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Textarea)->create([
            'label' => 'Your Message',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Your Message');
    });

    it('renders select field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Select)->create([
            'label' => 'Choose Option',
            'config' => [
                'options' => [
                    ['label' => 'Option A', 'value' => 'a'],
                    ['label' => 'Option B', 'value' => 'b'],
                ],
            ],
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Choose Option')
            ->assertSee('Option A')
            ->assertSee('Option B');
    });

    it('renders checkbox field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Checkbox)->create([
            'label' => 'I agree to the terms',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('I agree to the terms');
    });

    it('renders date field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Date)->create([
            'label' => 'Select Date',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Select Date');
    });

    it('renders number field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Number)->create([
            'label' => 'Your Age',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Your Age');
    });

    it('renders phone field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Phone)->create([
            'label' => 'Phone Number',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Phone Number');
    });

    it('renders file field', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::File)->create([
            'label' => 'Upload Document',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('Upload Document');
    });

    it('renders multiple fields in order', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'label' => 'First Name',
            'order' => 0,
        ]);
        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'label' => 'Email',
            'order' => 1,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSeeInOrder(['First Name', 'Email']);
    });
});

describe('form submission', function () {
    it('validates required fields', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->required()->create([
            'name' => 'name',
            'label' => 'Name',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.name', '')
            ->call('submit')
            ->assertHasErrors(['values.name']);
    });

    it('validates email format', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Email)->required()->create([
            'name' => 'email',
            'label' => 'Email',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['values.email']);
    });

    it('creates response on successful submission', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
            'label' => 'Name',
        ]);
        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'name' => 'email',
            'label' => 'Email',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.name', 'John Doe')
            ->set('values.email', 'john@example.com')
            ->call('submit')
            ->assertSet('submitted', true);

        expect(FormResponse::count())->toBe(1);
        $response = FormResponse::first();
        expect($response->form_id)->toBe($form->id);
        expect($response->submitter_email)->toBe('john@example.com');
        expect($response->submitter_name)->toBe('John Doe');
        expect($response->fieldValues)->toHaveCount(2);
    });

    it('stores field values correctly', function () {
        $form = Form::factory()->create();
        $textField = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
        ]);
        $checkboxField = FormField::factory()->forForm($form)->type(FormFieldType::Checkbox)->create([
            'name' => 'agree',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.name', 'Test Name')
            ->set('values.agree', true)
            ->call('submit');

        $response = FormResponse::first();
        $nameValue = $response->fieldValues->firstWhere('form_field_id', $textField->id);
        $checkValue = $response->fieldValues->firstWhere('form_field_id', $checkboxField->id);

        expect($nameValue->value)->toBe('Test Name');
        expect($checkValue->value)->toBe('1');
    });

    it('handles file upload', function () {
        Storage::fake('public');

        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::File)->create([
            'name' => 'document',
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('files.document', $file)
            ->call('submit');

        $response = FormResponse::first();
        $fileValue = $response->fieldValues->first();

        expect($fileValue->file_name)->toBe('document.pdf');
        expect($fileValue->file_path)->not->toBeNull();
        Storage::disk('public')->assertExists($fileValue->file_path);
    });

    it('shows success message after submission', function () {
        $form = Form::factory()->create([
            'success_message' => 'Thank you for your submission!',
        ]);
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.name', 'Test')
            ->call('submit')
            ->assertSee('Thank you for your submission!');
    });

    it('records ip address', function () {
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.name', 'Test')
            ->call('submit');

        $response = FormResponse::first();
        expect($response->ip_address)->not->toBeNull();
    });
});

describe('listing request integration', function () {
    it('associates response with listing request', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->set('values.name', 'Test')
            ->call('submit');

        $response = FormResponse::first();
        expect($response->listing_request_id)->toBe($listingRequest->id);

        $listingRequest->refresh();
        expect($listingRequest->custom_form_completed_at)->not->toBeNull();
    });

    it('can access custom form via requestee portal', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create(['is_active' => true]);
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create();

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        $this->get(route('listing-requests.custom-form', $listingRequest->access_token))
            ->assertOk();
    });

    it('denies access to custom form when not available', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create(['is_active' => true]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::Requested,
        ]);

        $this->get(route('listing-requests.custom-form', $listingRequest->access_token))
            ->assertForbidden();
    });

    it('denies access to custom form when no form assigned', function () {
        $listing = Listing::factory()->create();

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => null,
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        $this->get(route('listing-requests.custom-form', $listingRequest->access_token))
            ->assertForbidden();
    });
});

describe('form assignment', function () {
    it('listing request can have custom form', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
        ]);

        expect($listingRequest->customForm->id)->toBe($form->id);
    });

    it('can check if custom form is completed', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'custom_form_completed_at' => now(),
        ]);

        expect($listingRequest->hasCustomForm())->toBeTrue();
    });

    it('can check if custom form can be filled', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        expect($listingRequest->canFillCustomForm())->toBeTrue();

        $listingRequest->update(['custom_form_id' => null]);
        expect($listingRequest->canFillCustomForm())->toBeFalse();
    });
});

describe('auto-fill', function () {
    it('auto-fills fields from listing request', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'applicant_name',
            'config' => [
                'autofill_source' => 'listing_request',
                'autofill_field' => 'full_name',
            ],
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'name' => 'applicant_email',
            'config' => [
                'autofill_source' => 'listing_request',
                'autofill_field' => 'email',
            ],
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'first_name' => 'John',
            'middle_name' => null,
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->assertSet('values.applicant_name', 'John Doe')
            ->assertSet('values.applicant_email', 'john@example.com');
    });

    it('auto-fills fields from listing via listing request', function () {
        $listing = Listing::factory()->create([
            'city' => 'Berlin',
            'postal_code' => '10115',
        ]);

        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'city',
            'config' => [
                'autofill_source' => 'listing',
                'autofill_field' => 'city',
            ],
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->assertSet('values.city', 'Berlin');
    });

    it('leaves field empty when no auto-fill source available', function () {
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'name',
            'config' => [
                'autofill_source' => 'listing_request',
                'autofill_field' => 'full_name',
            ],
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSet('values.name', '');
    });
});

describe('employee prefill', function () {
    it('applies prefilled values from listing request', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'company_name',
            'label' => 'Company Name',
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'name' => 'email',
            'label' => 'Email',
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
            'form_prefilled_values' => [
                'company_name' => 'ACME Corp',
                'email' => 'contact@acme.com',
            ],
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->assertSet('values.company_name', 'ACME Corp')
            ->assertSet('values.email', 'contact@acme.com');
    });

    it('prefilled values override auto-fill values', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Email)->create([
            'name' => 'email',
            'config' => [
                'autofill_source' => 'listing_request',
                'autofill_field' => 'email',
            ],
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'email' => 'original@example.com',
            'status' => ListingRequestStatus::WaitingForInformation,
            'form_prefilled_values' => [
                'email' => 'prefilled@example.com',
            ],
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->assertSet('values.email', 'prefilled@example.com');
    });

    it('tracks locked fields from listing request', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'company_name',
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
            'form_prefilled_values' => ['company_name' => 'ACME Corp'],
            'form_locked_fields' => ['company_name'],
        ]);

        $component = Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest]);

        expect($component->instance()->isFieldLocked('company_name'))->toBeTrue();
        expect($component->instance()->isFieldLocked('other_field'))->toBeFalse();
    });

    it('renders locked field with disabled state', function () {
        $listing = Listing::factory()->create();
        $form = Form::factory()->create();

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'company_name',
            'label' => 'Company Name',
        ]);

        $listingRequest = ListingRequest::factory()->create([
            'listing_id' => $listing->id,
            'custom_form_id' => $form->id,
            'status' => ListingRequestStatus::WaitingForInformation,
            'form_prefilled_values' => ['company_name' => 'ACME Corp'],
            'form_locked_fields' => ['company_name'],
        ]);

        $component = Livewire::test(DynamicForm::class, ['form' => $form, 'listingRequest' => $listingRequest])
            ->assertSee('Company Name')
            ->assertSet('lockedFields', ['company_name']);

        // Verify the component correctly identifies locked fields
        expect($component->instance()->isFieldLocked('company_name'))->toBeTrue();
    });
});

describe('row layout', function () {
    it('renders fields inside a row', function () {
        $form = Form::factory()->create();

        // Create a row
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'name' => 'row_1',
            'config' => ['columns' => [1, 1]],
            'order' => 0,
        ]);

        // Create fields inside the row
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'first_name',
            'label' => 'First Name',
            'parent_id' => $row->id,
            'column_index' => 0,
            'order' => 0,
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'last_name',
            'label' => 'Last Name',
            'parent_id' => $row->id,
            'column_index' => 1,
            'order' => 0,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->assertSee('First Name')
            ->assertSee('Last Name');
    });

    it('initializes values for fields inside rows', function () {
        $form = Form::factory()->create();

        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'config' => ['columns' => [1, 1]],
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'nested_field',
            'parent_id' => $row->id,
            'column_index' => 0,
        ]);

        $component = Livewire::test(DynamicForm::class, ['form' => $form]);

        expect($component->instance()->values)->toHaveKey('nested_field');
    });

    it('validates fields inside rows', function () {
        $form = Form::factory()->create();

        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'config' => ['columns' => [1]],
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->required()->create([
            'name' => 'required_nested',
            'label' => 'Required Field',
            'parent_id' => $row->id,
            'column_index' => 0,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.required_nested', '')
            ->call('submit')
            ->assertHasErrors(['values.required_nested']);
    });

    it('submits form with nested field values', function () {
        $form = Form::factory()->create();

        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'config' => ['columns' => [1, 1]],
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'col1_field',
            'parent_id' => $row->id,
            'column_index' => 0,
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'col2_field',
            'parent_id' => $row->id,
            'column_index' => 1,
        ]);

        Livewire::test(DynamicForm::class, ['form' => $form])
            ->set('values.col1_field', 'Value 1')
            ->set('values.col2_field', 'Value 2')
            ->call('submit')
            ->assertSet('submitted', true);

        $response = FormResponse::first();
        expect($response->fieldValues)->toHaveCount(2);

        $col1Value = $response->fieldValues->firstWhere('value', 'Value 1');
        $col2Value = $response->fieldValues->firstWhere('value', 'Value 2');

        expect($col1Value)->not->toBeNull();
        expect($col2Value)->not->toBeNull();
    });

    it('does not create values for row fields', function () {
        $form = Form::factory()->create();

        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'name' => 'my_row',
            'config' => ['columns' => [1]],
        ]);

        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'name' => 'text_field',
            'parent_id' => $row->id,
            'column_index' => 0,
        ]);

        $component = Livewire::test(DynamicForm::class, ['form' => $form]);

        // Row should not have a value entry
        expect($component->instance()->values)->not->toHaveKey('my_row');
        // But text field should
        expect($component->instance()->values)->toHaveKey('text_field');
    });
});
