<?php

namespace App\Livewire\Pages;

use App\Enums\BlockType;
use App\Enums\PageLayout;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Builder extends Component
{
    use AuthorizesRequests;

    public Page $page;

    public ?string $selectedBlockId = null;

    public string $previewMode = 'desktop';

    public bool $showBlockPicker = false;

    public bool $showTemplatePicker = false;

    public ?string $insertAfterBlockId = null;

    public ?string $addingToParentId = null;

    // Page title (separate property to avoid binding issues)
    public string $pageTitle = '';

    // Block editor state
    public array $editingContent = [];

    public array $editingSettings = [];

    public function mount(Page $page): void
    {
        $this->authorize('update', $page);

        $this->page = $page->load('blocks.children');
        $this->pageTitle = $page->title;
    }

    public function getTitle(): string
    {
        return __('pages.builder.title', ['page' => $this->page->title]);
    }

    #[Computed]
    public function blockTypes(): array
    {
        return BlockType::grouped();
    }

    #[Computed]
    public function templates(): \Illuminate\Database\Eloquent\Collection
    {
        return PageTemplate::query()->active()->get();
    }

    #[Computed]
    public function layouts(): array
    {
        return PageLayout::cases();
    }

    public function selectBlock(string $blockId): void
    {
        $this->selectedBlockId = $blockId;

        $block = PageBlock::find($blockId);
        if ($block) {
            $this->editingContent = $block->content ?? [];
            $this->editingSettings = $block->settings ?? [];
        }
    }

    public function deselectBlock(): void
    {
        $this->selectedBlockId = null;
        $this->editingContent = [];
        $this->editingSettings = [];
    }

    public function addBlock(string $type, ?string $parentId = null): void
    {
        $blockType = BlockType::from($type);

        // Use addingToParentId if set (for adding to containers)
        $effectiveParentId = $this->addingToParentId ?? $parentId;

        $maxOrder = PageBlock::query()
            ->where('page_id', $this->page->id)
            ->when($effectiveParentId, fn ($q) => $q->where('parent_id', $effectiveParentId))
            ->when(! $effectiveParentId, fn ($q) => $q->whereNull('parent_id'))
            ->max('order') ?? -1;

        // If inserting after a specific block, calculate order
        $order = $maxOrder + 1;
        if ($this->insertAfterBlockId) {
            $afterBlock = PageBlock::find($this->insertAfterBlockId);
            if ($afterBlock) {
                $order = $afterBlock->order + 1;

                // Shift subsequent blocks
                PageBlock::query()
                    ->where('page_id', $this->page->id)
                    ->when($effectiveParentId, fn ($q) => $q->where('parent_id', $effectiveParentId))
                    ->when(! $effectiveParentId, fn ($q) => $q->whereNull('parent_id'))
                    ->where('order', '>=', $order)
                    ->increment('order');
            }
        }

        $block = PageBlock::create([
            'page_id' => $this->page->id,
            'parent_id' => $effectiveParentId,
            'type' => $blockType,
            'content' => $blockType->defaultContent(),
            'settings' => $blockType->defaultSettings(),
            'order' => $order,
            'column_span' => $effectiveParentId ? 1 : 12, // Default to 1 column for child blocks
        ]);

        $this->showBlockPicker = false;
        $this->insertAfterBlockId = null;
        $this->addingToParentId = null;
        $this->selectBlock($block->id);
        $this->refreshPage();
    }

    public function updateBlockContent(string $blockId): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $block->update([
            'content' => $this->editingContent,
        ]);

        $this->page->touch();
        $this->refreshPage();
    }

    public function updateBlockSettings(string $blockId): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $block->update([
            'settings' => $this->editingSettings,
        ]);

        $this->page->touch();
        $this->refreshPage();
    }

    public function updateBlockColumnSpan(string $blockId, int $span): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $block->update([
            'column_span' => min(12, max(1, $span)),
        ]);

        $this->page->touch();
        $this->refreshPage();
    }

    public function duplicateBlock(string $blockId): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $newBlock = $block->duplicate();
        $this->selectBlock($newBlock->id);
        $this->refreshPage();
    }

    public function deleteBlock(string $blockId): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        if ($this->selectedBlockId === $blockId) {
            $this->deselectBlock();
        }

        $block->delete();
        $this->page->touch();
        $this->refreshPage();
    }

    public function handleSort(string $id, int $position): void
    {
        $block = PageBlock::find($id);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $currentOrder = $block->order;

        if ($position === $currentOrder) {
            return;
        }

        // Get sibling blocks
        $siblings = PageBlock::query()
            ->where('page_id', $this->page->id)
            ->when($block->parent_id, fn ($q) => $q->where('parent_id', $block->parent_id))
            ->when(! $block->parent_id, fn ($q) => $q->whereNull('parent_id'))
            ->orderBy('order')
            ->get();

        // Reorder
        $reordered = $siblings->reject(fn ($b) => $b->id === $id)->values();
        $reordered->splice($position, 0, [$block]);

        foreach ($reordered as $index => $b) {
            if ($b->order !== $index) {
                $b->update(['order' => $index]);
            }
        }

        $this->page->touch();
        $this->refreshPage();
    }

    public function openBlockPicker(?string $afterBlockId = null): void
    {
        $this->insertAfterBlockId = $afterBlockId;
        $this->showBlockPicker = true;
    }

    public function closeBlockPicker(): void
    {
        $this->showBlockPicker = false;
        $this->insertAfterBlockId = null;
    }

    public function addBlockToParent(string $type, string $parentId): void
    {
        $this->addingToParentId = $parentId;
        $this->addBlock($type);
    }

    public function addBlockAtPosition(string $type, int $position, ?string $parentId = null): void
    {
        $blockType = BlockType::from($type);

        // Shift blocks at and after this position
        PageBlock::query()
            ->where('page_id', $this->page->id)
            ->when($parentId, fn ($q) => $q->where('parent_id', $parentId))
            ->when(! $parentId, fn ($q) => $q->whereNull('parent_id'))
            ->where('order', '>=', $position)
            ->increment('order');

        $block = PageBlock::create([
            'page_id' => $this->page->id,
            'parent_id' => $parentId,
            'type' => $blockType,
            'content' => $blockType->defaultContent(),
            'settings' => $blockType->defaultSettings(),
            'order' => $position,
            'column_span' => $parentId ? 1 : 12,
        ]);

        $this->selectBlock($block->id);
        $this->refreshPage();
    }

    /**
     * Move an existing block to become a child of a container block.
     */
    public function moveBlockToParent(string $blockId, string $parentId): void
    {
        $block = PageBlock::find($blockId);
        $parent = PageBlock::find($parentId);

        if (! $block || ! $parent || $block->page_id !== $this->page->id || $parent->page_id !== $this->page->id) {
            return;
        }

        // Prevent moving a block into itself or its descendants
        if ($blockId === $parentId || $this->isDescendantOf($parentId, $blockId)) {
            return;
        }

        // Prevent moving a container block into a non-container
        if (! $parent->type->supportsChildren()) {
            return;
        }

        // Reorder siblings in the old location (close the gap)
        PageBlock::query()
            ->where('page_id', $this->page->id)
            ->when($block->parent_id, fn ($q) => $q->where('parent_id', $block->parent_id))
            ->when(! $block->parent_id, fn ($q) => $q->whereNull('parent_id'))
            ->where('order', '>', $block->order)
            ->decrement('order');

        // Calculate new order in the parent
        $maxOrder = PageBlock::query()
            ->where('page_id', $this->page->id)
            ->where('parent_id', $parentId)
            ->max('order') ?? -1;

        // Move the block
        $block->update([
            'parent_id' => $parentId,
            'order' => $maxOrder + 1,
            'column_span' => 1, // Default to 1 column when becoming a child
        ]);

        $this->page->touch();
        $this->refreshPage();
    }

    /**
     * Check if a block is a descendant of another block.
     */
    private function isDescendantOf(string $potentialDescendantId, string $ancestorId): bool
    {
        $block = PageBlock::find($potentialDescendantId);

        while ($block && $block->parent_id) {
            if ($block->parent_id === $ancestorId) {
                return true;
            }
            $block = PageBlock::find($block->parent_id);
        }

        return false;
    }

    public function applyTemplate(string $templateId): void
    {
        $template = PageTemplate::find($templateId);
        if (! $template) {
            return;
        }

        // Delete existing blocks
        $this->page->allBlocks()->delete();

        // Create blocks from template structure
        foreach ($template->structure as $index => $blockData) {
            $blockType = BlockType::from($blockData['type']);

            PageBlock::create([
                'page_id' => $this->page->id,
                'type' => $blockType,
                'content' => $blockData['content'] ?? $blockType->defaultContent(),
                'settings' => $blockData['settings'] ?? $blockType->defaultSettings(),
                'order' => $index,
                'column_span' => $blockData['column_span'] ?? 12,
            ]);
        }

        $this->showTemplatePicker = false;
        $this->deselectBlock();
        $this->refreshPage();
    }

    public function updatedPageTitle(): void
    {
        if (empty($this->pageTitle)) {
            return;
        }

        $this->page->update([
            'title' => $this->pageTitle,
            'slug' => Str::slug($this->pageTitle),
            'updated_by' => auth()->id(),
        ]);
    }

    public function updatePageLayout(string $layout): void
    {
        $this->page->update([
            'layout' => PageLayout::from($layout),
            'updated_by' => auth()->id(),
        ]);

        $this->refreshPage();
    }

    public function updatePageMeta(string $description, string $keywords): void
    {
        $this->page->update([
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'updated_by' => auth()->id(),
        ]);
    }

    public function savePage(): void
    {
        $this->page->update([
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('page-saved');
    }

    public function publishPage(): void
    {
        $this->authorize('publish', $this->page);

        $this->page->publish();
        $this->page->update(['updated_by' => auth()->id()]);

        $this->dispatch('page-published');
    }

    public function unpublishPage(): void
    {
        $this->authorize('publish', $this->page);

        $this->page->unpublish();
        $this->page->update(['updated_by' => auth()->id()]);

        $this->dispatch('page-unpublished');
    }

    public function setPreviewMode(string $mode): void
    {
        $this->previewMode = $mode;
    }

    #[On('block-content-updated')]
    public function onBlockContentUpdated(string $blockId, array $content): void
    {
        $this->editingContent = $content;
        $this->updateBlockContent($blockId);
    }

    public function updateInlineText(string $blockId, string $field, string $value): void
    {
        $block = PageBlock::find($blockId);
        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $content = $block->content ?? [];
        $content[$field] = $value;

        $block->update(['content' => $content]);

        // Sync with sidebar if this block is selected
        if ($this->selectedBlockId === $blockId) {
            $this->editingContent = $content;
        }

        $this->page->touch();
    }

    /**
     * Auto-save when editingContent changes.
     */
    public function updatedEditingContent(): void
    {
        if ($this->selectedBlockId) {
            $this->updateBlockContent($this->selectedBlockId);
        }
    }

    /**
     * Auto-save when editingSettings changes.
     */
    public function updatedEditingSettings(): void
    {
        if ($this->selectedBlockId) {
            $this->updateBlockSettings($this->selectedBlockId);
        }
    }

    private function refreshPage(): void
    {
        $this->page->refresh();
        $this->page->load('blocks.children');
    }

    public function render(): View
    {
        return view('livewire.pages.builder')
            ->title($this->getTitle());
    }
}
