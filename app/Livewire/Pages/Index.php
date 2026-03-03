<?php

namespace App\Livewire\Pages;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('pages.index.title')]
class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Page::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function statuses(): array
    {
        return PageStatus::cases();
    }

    public function deletePage(Page $page): void
    {
        $this->authorize('delete', $page);

        $page->delete();

        $this->dispatch('page-deleted');
    }

    public function duplicatePage(Page $page): void
    {
        $this->authorize('create', Page::class);

        $newPage = $page->replicate();
        $newPage->title = $page->title.' (Copy)';
        $newPage->slug = $page->slug.'-copy-'.now()->timestamp;
        $newPage->status = PageStatus::Draft;
        $newPage->published_at = null;
        $newPage->created_by = auth()->id();
        $newPage->updated_by = null;
        $newPage->save();

        // Duplicate blocks
        foreach ($page->allBlocks as $block) {
            $newBlock = $block->replicate();
            $newBlock->page_id = $newPage->id;
            $newBlock->save();
        }

        $this->redirect(route('pages.builder', $newPage), navigate: true);
    }

    public function render(): View
    {
        $pages = Page::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->with(['creator'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('livewire.pages.index', [
            'pages' => $pages,
        ]);
    }
}
