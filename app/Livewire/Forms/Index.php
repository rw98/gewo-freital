<?php

namespace App\Livewire\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormField;
use App\Services\AiFormService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public bool $showCreateModal = false;

    public string $newFormName = '';

    public string $newFormDescription = '';

    // AI Import
    public bool $showAiImportModal = false;

    public $pdfFile = null;

    public string $aiFormName = '';

    public string $aiPrompt = '';

    public bool $isProcessing = false;

    public ?string $aiError = null;

    public function getTitle(): string
    {
        return __('forms.index.title');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['newFormName', 'newFormDescription']);
        $this->showCreateModal = true;
    }

    public function openAiImportModal(): void
    {
        $this->reset(['pdfFile', 'aiFormName', 'aiPrompt', 'aiError']);
        $this->showAiImportModal = true;
    }

    public function createForm(): void
    {
        $this->validate([
            'newFormName' => ['required', 'string', 'max:255'],
            'newFormDescription' => ['nullable', 'string', 'max:5000'],
        ]);

        $form = Form::create([
            'name' => $this->newFormName,
            'slug' => Str::slug($this->newFormName),
            'description' => $this->newFormDescription ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->redirect(route('forms.builder', $form), navigate: true);
    }

    public function importFromPdf(): void
    {
        $this->validate([
            'pdfFile' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'aiFormName' => ['required', 'string', 'max:255'],
            'aiPrompt' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->isProcessing = true;
        $this->aiError = null;

        try {
            // Create a temp file and copy the uploaded PDF content
            $tempFile = tmpfile();
            $tempPath = stream_get_meta_data($tempFile)['uri'];
            file_put_contents($tempPath, file_get_contents($this->pdfFile->getRealPath()));

            // Call AI service
            $service = app(AiFormService::class);
            $fields = $service->analyzeAndGenerateFields($tempPath, $this->aiPrompt ?: null);

            // Clean up temp file
            fclose($tempFile);

            if (empty($fields)) {
                throw new \RuntimeException(__('forms.ai.no_fields_found'));
            }

            // Create the form
            $form = Form::create([
                'name' => $this->aiFormName,
                'slug' => Str::slug($this->aiFormName),
                'description' => __('forms.ai.generated_description'),
                'created_by' => auth()->id(),
            ]);

            // Create fields
            foreach ($fields as $index => $fieldData) {
                $fieldType = FormFieldType::tryFrom($fieldData['type']) ?? FormFieldType::Text;

                FormField::create([
                    'form_id' => $form->id,
                    'type' => $fieldType,
                    'name' => $fieldData['name'],
                    'label' => $fieldData['label'],
                    'description' => $fieldData['description'],
                    'placeholder' => $fieldData['placeholder'],
                    'config' => $fieldData['config'] ?? $fieldType->defaultConfig(),
                    'validation_rules' => [],
                    'order' => $index,
                    'is_required' => $fieldData['is_required'] ?? false,
                ]);
            }

            $this->showAiImportModal = false;
            $this->redirect(route('forms.builder', $form), navigate: true);

        } catch (\Exception $e) {
            throw $e;
            $this->aiError = $e->getMessage();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function deleteForm(Form $form): void
    {
        $form->delete();
        $this->dispatch('form-deleted');
    }

    public function duplicateForm(Form $form): void
    {
        $newForm = $form->replicate();
        $newForm->name = $form->name.' (Kopie)';
        $newForm->slug = $form->slug.'-copy-'.now()->timestamp;
        $newForm->key = Str::random(32);
        $newForm->created_by = auth()->id();
        $newForm->save();

        // Duplicate fields
        foreach ($form->fields as $field) {
            $newField = $field->replicate();
            $newField->form_id = $newForm->id;
            $newField->save();
        }

        $this->redirect(route('forms.builder', $newForm), navigate: true);
    }

    public function toggleActive(Form $form): void
    {
        $form->update(['is_active' => ! $form->is_active]);
    }

    public function render(): View
    {
        $forms = Form::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->with(['creator'])
            ->withCount(['fields', 'responses'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('livewire.forms.index', [
            'forms' => $forms,
        ])->title($this->getTitle());
    }
}
