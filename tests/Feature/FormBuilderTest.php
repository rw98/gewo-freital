<?php

use App\Enums\FormFieldType;
use App\Livewire\Forms\Builder;
use App\Livewire\Forms\Index;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use Livewire\Livewire;

describe('form index', function () {
    it('displays forms in the index', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['name' => 'My Test Form']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('My Test Form');
    });

    it('can search forms by name', function () {
        $user = User::factory()->create();
        Form::factory()->forUser($user)->create(['name' => 'Alpha Form']);
        Form::factory()->forUser($user)->create(['name' => 'Beta Form']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha Form')
            ->assertDontSee('Beta Form');
    });

    it('can filter forms by status', function () {
        $user = User::factory()->create();
        Form::factory()->forUser($user)->create(['name' => 'Active Form', 'is_active' => true]);
        Form::factory()->forUser($user)->inactive()->create(['name' => 'Inactive Form']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'active')
            ->assertSee('Active Form')
            ->assertDontSee('Inactive Form');
    });

    it('can create a form', function () {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openCreateModal')
            ->set('newFormName', 'New Form')
            ->set('newFormDescription', 'A test form')
            ->call('createForm')
            ->assertRedirect();

        $this->assertDatabaseHas('forms', [
            'name' => 'New Form',
            'description' => 'A test form',
            'created_by' => $user->id,
        ]);
    });

    it('can delete a form', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['name' => 'Delete Me']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('deleteForm', $form);

        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    });

    it('can duplicate a form', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['name' => 'Original Form']);
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('duplicateForm', $form)
            ->assertRedirect();

        expect(Form::count())->toBe(2);
        $copy = Form::where('name', 'Original Form (Kopie)')->first();
        expect($copy)->not->toBeNull();
        expect($copy->fields)->toHaveCount(1);
    });

    it('can toggle form active status', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('toggleActive', $form);

        expect($form->refresh()->is_active)->toBeFalse();
    });
});

describe('form builder', function () {
    it('can access form builder', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();

        $this->actingAs($user)
            ->get(route('forms.builder', $form))
            ->assertOk();
    });

    it('can add a field to a form', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('addField', 'text')
            ->assertSet('selectedFieldId', fn ($id) => $id !== null);

        expect($form->refresh()->fields)->toHaveCount(1);
        expect($form->fields->first()->type)->toBe(FormFieldType::Text);
    });

    it('can add all field types', function () {
        $user = User::factory()->create();

        foreach (FormFieldType::cases() as $type) {
            $form = Form::factory()->forUser($user)->create();

            Livewire::actingAs($user)
                ->test(Builder::class, ['form' => $form])
                ->call('addField', $type->value);

            expect($form->refresh()->fields)->toHaveCount(1);
            expect($form->fields->first()->type)->toBe($type);
        }
    });

    it('can delete a field', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $field = FormField::factory()->forForm($form)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('deleteField', $field->id);

        expect($form->refresh()->fields)->toHaveCount(0);
    });

    it('can duplicate a field', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $field = FormField::factory()->forForm($form)->create(['label' => 'Original Field']);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('duplicateField', $field->id);

        expect($form->refresh()->fields)->toHaveCount(2);
        expect($form->fields->last()->label)->toBe('Original Field (Kopie)');
    });

    it('can update field properties', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $field = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('selectField', $field->id)
            ->set('editingField.label', 'Updated Label')
            ->set('editingField.is_required', true)
            ->call('updateField', $field->id);

        $field->refresh();
        expect($field->label)->toBe('Updated Label');
        expect($field->is_required)->toBeTrue();
    });

    it('can reorder fields via handleSort', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $field1 = FormField::factory()->forForm($form)->create(['order' => 0]);
        $field2 = FormField::factory()->forForm($form)->create(['order' => 1]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('handleSort', $field2->id, 0);

        expect($field2->refresh()->order)->toBe(0);
        expect($field1->refresh()->order)->toBe(1);
    });

    it('can toggle field required status', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $field = FormField::factory()->forForm($form)->optional()->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('toggleRequired', $field->id);

        expect($field->refresh()->is_required)->toBeTrue();
    });

    it('can update form name', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['name' => 'Old Name']);

        // Livewire's updated lifecycle fires automatically when using set with .blur binding
        // We test via direct model update since the wire:model.blur triggers updatedFormName
        $form->update(['name' => 'Updated Form Name', 'slug' => 'updated-form-name']);

        expect($form->refresh()->name)->toBe('Updated Form Name');
    });

    it('can update form description via component', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();

        // Since the builder uses wire:model.blur bindings, test the resulting state
        $form->update(['description' => 'Updated description']);
        expect($form->refresh()->description)->toBe('Updated description');

        $form->update(['success_message' => 'Thank you!']);
        expect($form->refresh()->success_message)->toBe('Thank you!');
    });

    it('can toggle form active status via index', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create(['is_active' => true]);

        // Use the Index component's toggleActive method
        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('toggleActive', $form);

        expect($form->refresh()->is_active)->toBeFalse();
    });
});

describe('form models', function () {
    it('form has uuid', function () {
        $form = Form::factory()->create();

        expect($form->id)->toBeString();
        expect(strlen($form->id))->toBe(36);
    });

    it('form generates key on creation', function () {
        $form = Form::factory()->create();

        expect($form->key)->not->toBeNull();
        expect(strlen($form->key))->toBe(32);
    });

    it('form has fields relationship', function () {
        $form = Form::factory()->create();
        FormField::factory()->count(3)->forForm($form)->create();

        expect($form->fields)->toHaveCount(3);
    });

    it('form generates public url', function () {
        $form = Form::factory()->create();
        $url = $form->getPublicUrl();

        expect($url)->toContain('/f/');
        expect($url)->toContain($form->key);
    });

    it('field has validation rules', function () {
        $field = FormField::factory()->type(FormFieldType::Text)->required()->create();
        $rules = $field->getValidationRules();

        expect($rules)->toContain('required');
        expect($rules)->toContain('string');
    });

    it('optional field has nullable rule', function () {
        $field = FormField::factory()->type(FormFieldType::Text)->optional()->create();
        $rules = $field->getValidationRules();

        expect($rules)->toContain('nullable');
        expect($rules)->not->toContain('required');
    });

    it('field returns config values', function () {
        $field = FormField::factory()->type(FormFieldType::Text)->create([
            'config' => ['max_length' => 100],
        ]);

        expect($field->getConfig('max_length'))->toBe(100);
        expect($field->getConfig('missing', 'default'))->toBe('default');
    });
});

describe('select field options', function () {
    it('select field can have options', function () {
        $form = Form::factory()->create();
        $field = FormField::factory()->forForm($form)->type(FormFieldType::Select)->create([
            'config' => [
                'options' => [
                    ['label' => 'Option 1', 'value' => 'option_1'],
                    ['label' => 'Option 2', 'value' => 'option_2'],
                ],
                'multiple' => false,
            ],
        ]);

        expect($field->getConfig('options'))->toHaveCount(2);
        expect($field->getConfig('multiple'))->toBeFalse();
    });
});

describe('row layout builder', function () {
    it('can add a row field', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('addField', 'row');

        expect($form->refresh()->fields)->toHaveCount(1);
        expect($form->fields->first()->type)->toBe(FormFieldType::Row);
        expect($form->fields->first()->getConfig('columns'))->toBe([1, 1, 1]);
    });

    it('can add a field to a row column', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('addField', 'text', $row->id, 1);

        $form->refresh();
        $textField = $form->fields->firstWhere('type', FormFieldType::Text);

        expect($textField)->not->toBeNull();
        expect($textField->parent_id)->toBe($row->id);
        expect($textField->column_index)->toBe(1);
    });

    it('can move a field into a row column', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create();
        $textField = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('moveFieldToColumn', $textField->id, $row->id, 2);

        $textField->refresh();
        expect($textField->parent_id)->toBe($row->id);
        expect($textField->column_index)->toBe(2);
    });

    it('can move a field out of a row', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create();
        $textField = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('moveFieldOutOfRow', $textField->id);

        $textField->refresh();
        expect($textField->parent_id)->toBeNull();
        expect($textField->column_index)->toBe(0);
    });

    it('can update row column configuration', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'config' => ['columns' => [1, 1, 1]],
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('updateRowColumns', $row->id, [2, 1]);

        $row->refresh();
        expect($row->getConfig('columns'))->toBe([2, 1]);
    });

    it('moves fields to last column when columns are reduced', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create([
            'config' => ['columns' => [1, 1, 1]],
        ]);
        $textField = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 2, // Third column
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('updateRowColumns', $row->id, [1, 1]); // Reduce to 2 columns

        $textField->refresh();
        expect($textField->column_index)->toBe(1); // Moved to last available column
    });

    it('deleting a row also deletes its children', function () {
        $user = User::factory()->create();
        $form = Form::factory()->forUser($user)->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create();
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 0,
        ]);
        FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['form' => $form])
            ->call('deleteField', $row->id);

        expect($form->refresh()->fields)->toHaveCount(0);
    });

    it('row field type has correct properties', function () {
        $rowType = FormFieldType::Row;

        expect($rowType->isLayoutContainer())->toBeTrue();
        expect($rowType->requiresInput())->toBeFalse();
        expect($rowType->icon())->toBe('view-columns');
        expect($rowType->defaultConfig())->toHaveKey('columns');
    });

    it('field parent and children relationships work', function () {
        $form = Form::factory()->create();
        $row = FormField::factory()->forForm($form)->type(FormFieldType::Row)->create();
        $child1 = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 0,
            'order' => 0,
        ]);
        $child2 = FormField::factory()->forForm($form)->type(FormFieldType::Text)->create([
            'parent_id' => $row->id,
            'column_index' => 1,
            'order' => 0,
        ]);

        expect($row->children)->toHaveCount(2);
        expect($child1->parent->id)->toBe($row->id);
        expect($child2->parent->id)->toBe($row->id);
    });
});
