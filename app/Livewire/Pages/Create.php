<?php

namespace App\Livewire\Pages;

use App\Enums\BlockType;
use App\Enums\PageLayout;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageTemplate;
use App\Services\PageImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('pages.create.title')]
class Create extends Component
{
    use AuthorizesRequests;

    public string $title = '';

    public string $slug = '';

    public string $layout = 'default';

    public ?string $templateId = null;

    // Import functionality
    public string $importUrl = '';

    public bool $isImporting = false;

    public ?string $importError = null;

    /** @var array<int, array<string, mixed>> */
    public array $importedBlocks = [];

    public ?string $importedTitle = null;

    public function mount(): void
    {
        $this->authorize('create', Page::class);
    }

    public function updatedTitle(): void
    {
        $this->slug = Str::slug($this->title);
    }

    #[Computed]
    public function layouts(): array
    {
        return PageLayout::cases();
    }

    #[Computed]
    public function templates(): \Illuminate\Database\Eloquent\Collection
    {
        return PageTemplate::query()->active()->get();
    }

    public function selectTemplate(?string $templateId): void
    {
        $this->templateId = $templateId;
        // Clear import when selecting template
        $this->clearImport();
    }

    /**
     * Import content from a URL using AI.
     */
    public function importFromUrl(PageImportService $importService): void
    {
        $this->validate([
            'importUrl' => ['required', 'url'],
        ]);

        $this->isImporting = true;
        $this->importError = null;
        $this->importedBlocks = [];

        try {
            // Fetch content from URL
            $content = $importService->fetchContent($this->importUrl);

            // Generate blocks using AI (including images, tables, and layout structure)
            $this->importedBlocks = $importService->generateBlocks(
                $content['content'],
                $content['title'],
                $content['url'],
                $content['images'] ?? [],
                $content['tables'] ?? [],
                $content['layoutSections'] ?? []
            );

            // Set title from imported content if empty
            if (empty($this->title) && ! empty($content['title'])) {
                $this->importedTitle = $content['title'];
                $this->title = $content['title'];
                $this->slug = Str::slug($this->title);
            }

            // Clear template selection when importing
            $this->templateId = null;

        } catch (\Exception $e) {
            $this->importError = $e->getMessage();
        } finally {
            $this->isImporting = false;
        }
    }

    /**
     * Clear imported content.
     */
    public function clearImport(): void
    {
        $this->importUrl = '';
        $this->importedBlocks = [];
        $this->importedTitle = null;
        $this->importError = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'layout' => ['required', Rule::enum(PageLayout::class)],
        ];
    }

    public function create(): void
    {
        $this->authorize('create', Page::class);

        $validated = $this->validate();

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'layout' => $validated['layout'],
            'status' => PageStatus::Draft,
            'created_by' => auth()->id(),
        ]);

        // Apply imported blocks if available
        if (! empty($this->importedBlocks)) {
            $this->createBlocksRecursively($page, $this->importedBlocks);
        }
        // Apply template if selected (and no imported blocks)
        elseif ($this->templateId) {
            $template = PageTemplate::find($this->templateId);
            if ($template) {
                $this->createBlocksRecursively($page, $template->structure);
            }
        }

        $this->redirect(route('pages.builder', $page), navigate: true);
    }

    /**
     * Recursively create blocks with their children.
     *
     * @param  array<int, array<string, mixed>>  $blocksData
     */
    private function createBlocksRecursively(Page $page, array $blocksData, ?string $parentId = null): void
    {
        foreach ($blocksData as $index => $blockData) {
            $blockType = BlockType::from($blockData['type']);

            $block = PageBlock::create([
                'page_id' => $page->id,
                'parent_id' => $parentId,
                'type' => $blockType,
                'content' => $blockData['content'] ?? $blockType->defaultContent(),
                'settings' => $blockData['settings'] ?? $blockType->defaultSettings(),
                'order' => $index,
                'column_span' => $blockData['column_span'] ?? 12,
            ]);

            // Recursively create children if present
            if (! empty($blockData['children']) && is_array($blockData['children'])) {
                $this->createBlocksRecursively($page, $blockData['children'], $block->id);
            }
        }
    }

    public function render(): View
    {
        return view('livewire.pages.create');
    }
}
