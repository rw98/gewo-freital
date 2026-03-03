<?php

namespace App\Livewire\Pages;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class PublicShow extends Component
{
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::query()
            ->where('slug', $slug)
            ->where('status', PageStatus::Published)
            ->whereNotNull('published_at')
            ->with(['blocks.children'])
            ->firstOrFail();
    }

    public function getTitle(): string
    {
        return $this->page->title;
    }

    public function render(): View
    {
        return view('livewire.pages.public-show')
            ->title($this->getTitle())
            ->with([
                'metaDescription' => $this->page->meta_description,
                'metaKeywords' => $this->page->meta_keywords,
            ]);
    }
}
