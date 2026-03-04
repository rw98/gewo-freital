<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Services\FormAutoFillService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Builder extends Component
{
    public Form $form;

    public ?string $selectedFieldId = null;

    public bool $showFieldPicker = false;

    // Form settings
    public string $formName = '';

    public string $formDescription = '';

    public string $successMessage = '';

    public bool $isActive = true;

    // Field editor state
    public array $editingField = [];

    public function mount(Form $form): void
    {
        $this->form = $form->load('fields');
        $this->formName = $form->name;
        $this->formDescription = $form->description ?? '';
        $this->successMessage = $form->success_message ?? '';
        $this->isActive = $form->is_active;
    }

    public function getTitle(): string
    {
        return __('forms.builder.title', ['form' => $this->form->name]);
    }

    #[Computed]
    public function fieldTypes(): array
    {
        return FormFieldType::cases();
    }

    #[Computed]
    public function autoFillSourceTypes(): array
    {
        return FormAutoFillService::sourceTypes();
    }

    #[Computed]
    public function autoFillSourceFields(): array
    {
        return [
            'listing' => FormAutoFillService::fieldsForSource('listing'),
            'listing_request' => FormAutoFillService::fieldsForSource('listing_request'),
            'flat' => FormAutoFillService::fieldsForSource('flat'),
        ];
    }

    public function selectField(string $fieldId): void
    {
        $this->selectedFieldId = $fieldId;

        $field = FormField::find($fieldId);
        if ($field) {
            $this->editingField = [
                'label' => $field->label,
                'name' => $field->name,
                'description' => $field->description ?? '',
                'placeholder' => $field->placeholder ?? '',
                'is_required' => $field->is_required,
                'config' => $field->config ?? [],
            ];
        }
    }

    public function deselectField(): void
    {
        $this->selectedFieldId = null;
        $this->editingField = [];
    }

    public function addField(string $type, ?string $parentId = null, int $columnIndex = 0): void
    {
        $fieldType = FormFieldType::from($type);

        $maxOrder = FormField::query()
            ->where('form_id', $this->form->id)
            ->where('parent_id', $parentId)
            ->when($parentId !== null, fn ($q) => $q->where('column_index', $columnIndex))
            ->max('order') ?? -1;

        $field = FormField::create([
            'form_id' => $this->form->id,
            'parent_id' => $parentId,
            'column_index' => $columnIndex,
            'type' => $fieldType,
            'name' => Str::snake($fieldType->label()).'_'.Str::random(4),
            'label' => $fieldType->label(),
            'config' => $fieldType->defaultConfig(),
            'validation_rules' => [],
            'order' => $maxOrder + 1,
            'is_required' => false,
        ]);

        $this->showFieldPicker = false;
        $this->selectField($field->id);
        $this->refreshForm();
    }

    public function addFieldToRow(string $rowId, int $columnIndex): void
    {
        $this->dispatch('open-field-picker', rowId: $rowId, columnIndex: $columnIndex);
    }

    public function updateField(string $fieldId): void
    {
        $field = FormField::find($fieldId);
        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        $field->update([
            'label' => $this->editingField['label'] ?? $field->label,
            'name' => $this->editingField['name'] ?? $field->name,
            'description' => $this->editingField['description'] ?? null,
            'placeholder' => $this->editingField['placeholder'] ?? null,
            'is_required' => $this->editingField['is_required'] ?? false,
            'config' => $this->editingField['config'] ?? [],
        ]);

        $this->form->touch();
        $this->refreshForm();
    }

    public function duplicateField(string $fieldId): void
    {
        $field = FormField::find($fieldId);
        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        $maxOrder = FormField::query()
            ->where('form_id', $this->form->id)
            ->max('order') ?? -1;

        $newField = $field->replicate();
        $newField->name = $field->name.'_copy';
        $newField->label = $field->label.' (Kopie)';
        $newField->order = $maxOrder + 1;
        $newField->save();

        $this->selectField($newField->id);
        $this->refreshForm();
    }

    public function deleteField(string $fieldId): void
    {
        $field = FormField::find($fieldId);
        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        if ($this->selectedFieldId === $fieldId) {
            $this->deselectField();
        }

        $field->delete();
        $this->form->touch();
        $this->refreshForm();
    }

    public function handleSort(string $id, int $position, ?string $parentId = null, int $columnIndex = 0): void
    {
        $field = FormField::find($id);
        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        // Update parent and column if moving to a different location
        $parentChanged = $field->parent_id !== $parentId || $field->column_index !== $columnIndex;

        if ($parentChanged) {
            $field->update([
                'parent_id' => $parentId,
                'column_index' => $columnIndex,
            ]);
        }

        // Get fields in the target location
        $query = FormField::query()
            ->where('form_id', $this->form->id)
            ->where('parent_id', $parentId);

        if ($parentId !== null) {
            $query->where('column_index', $columnIndex);
        }

        $fields = $query->orderBy('order')->get();

        $reordered = $fields->reject(fn ($f) => $f->id === $id)->values();
        $reordered->splice($position, 0, [$field->fresh()]);

        foreach ($reordered as $index => $f) {
            if ($f->order !== $index) {
                $f->update(['order' => $index]);
            }
        }

        $this->form->touch();
        $this->refreshForm();
    }

    public function moveFieldToColumn(string $fieldId, string $rowId, int $columnIndex): void
    {
        $field = FormField::find($fieldId);
        $row = FormField::find($rowId);

        if (! $field || ! $row || $field->form_id !== $this->form->id) {
            return;
        }

        // Don't allow moving a row into itself or its children
        if ($field->type === FormFieldType::Row && $this->isDescendant($field, $row)) {
            return;
        }

        $maxOrder = FormField::query()
            ->where('form_id', $this->form->id)
            ->where('parent_id', $rowId)
            ->where('column_index', $columnIndex)
            ->max('order') ?? -1;

        $field->update([
            'parent_id' => $rowId,
            'column_index' => $columnIndex,
            'order' => $maxOrder + 1,
        ]);

        $this->form->touch();
        $this->refreshForm();
    }

    public function moveFieldOutOfRow(string $fieldId): void
    {
        $field = FormField::find($fieldId);

        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        $maxOrder = FormField::query()
            ->where('form_id', $this->form->id)
            ->whereNull('parent_id')
            ->max('order') ?? -1;

        $field->update([
            'parent_id' => null,
            'column_index' => 0,
            'order' => $maxOrder + 1,
        ]);

        $this->form->touch();
        $this->refreshForm();
    }

    private function isDescendant(FormField $ancestor, FormField $field): bool
    {
        if ($field->parent_id === null) {
            return false;
        }

        if ($field->parent_id === $ancestor->id) {
            return true;
        }

        $parent = FormField::find($field->parent_id);

        return $parent ? $this->isDescendant($ancestor, $parent) : false;
    }

    public function updateRowColumns(string $fieldId, array $columns): void
    {
        $field = FormField::find($fieldId);
        if (! $field || $field->form_id !== $this->form->id || $field->type !== FormFieldType::Row) {
            return;
        }

        $config = $field->config ?? [];
        $config['columns'] = $columns;

        $field->update(['config' => $config]);

        // Move fields from removed columns to the last available column
        $columnCount = count($columns);
        FormField::query()
            ->where('parent_id', $fieldId)
            ->where('column_index', '>=', $columnCount)
            ->update(['column_index' => $columnCount - 1]);

        $this->form->touch();
        $this->refreshForm();

        if ($this->selectedFieldId === $fieldId) {
            $this->editingField['config'] = $config;
        }
    }

    public function toggleRequired(string $fieldId): void
    {
        $field = FormField::find($fieldId);
        if (! $field || $field->form_id !== $this->form->id) {
            return;
        }

        $field->update(['is_required' => ! $field->is_required]);

        if ($this->selectedFieldId === $fieldId) {
            $this->editingField['is_required'] = $field->is_required;
        }

        $this->refreshForm();
    }

    public function updatedFormName(): void
    {
        if (empty($this->formName)) {
            return;
        }

        $this->form->update([
            'name' => $this->formName,
            'slug' => Str::slug($this->formName),
        ]);
    }

    public function updatedFormDescription(): void
    {
        $this->form->update([
            'description' => $this->formDescription ?: null,
        ]);
    }

    public function updatedSuccessMessage(): void
    {
        $this->form->update([
            'success_message' => $this->successMessage ?: null,
        ]);
    }

    public function updatedIsActive(): void
    {
        $this->form->update([
            'is_active' => $this->isActive,
        ]);
    }

    #[On('field-updated')]
    public function onFieldUpdated(): void
    {
        if ($this->selectedFieldId) {
            $this->updateField($this->selectedFieldId);
        }
    }

    public function updatedEditingField(): void
    {
        if ($this->selectedFieldId) {
            $this->updateField($this->selectedFieldId);
        }
    }

    public function copyPublicUrl(): void
    {
        $this->dispatch('copy-to-clipboard', url: $this->form->getPublicUrl());
    }

    #[Computed]
    public function topLevelFields()
    {
        return $this->form->fields
            ->whereNull('parent_id')
            ->sortBy('order')
            ->values();
    }

    private function refreshForm(): void
    {
        $this->form->refresh();
        $this->form->load(['fields', 'fields.children']);
    }

    public function render(): View
    {
        return view('livewire.forms.builder')
            ->title($this->getTitle());
    }
}
