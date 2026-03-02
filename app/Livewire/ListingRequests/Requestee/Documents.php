<?php

namespace App\Livewire\ListingRequests\Requestee;

use App\Enums\RequestDocumentType;
use App\Models\ListingRequest;
use App\Models\RequestDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.auth.public')]
class Documents extends Component
{
    use WithFileUploads;

    public ListingRequest $listingRequest;

    #[Validate('required')]
    public string $document_type = '';

    #[Validate('required|file|mimes:pdf,jpg,jpeg,png|max:10240')]
    public $document;

    public function mount(string $access_token): void
    {
        $this->listingRequest = ListingRequest::query()
            ->where('access_token', $access_token)
            ->with(['listing', 'documents'])
            ->firstOrFail();
    }

    public function getTitle(): string
    {
        return __('listing_requests.documents').' - '.$this->listingRequest->listing->title;
    }

    #[Computed]
    public function documentTypes(): array
    {
        return RequestDocumentType::cases();
    }

    public function upload(): void
    {
        $this->validate();

        $filename = Str::uuid().'.'.$this->document->getClientOriginalExtension();
        $path = $this->document->storeAs('request-documents/'.$this->listingRequest->id, $filename, 'private');

        RequestDocument::create([
            'listing_request_id' => $this->listingRequest->id,
            'uploaded_by_user_id' => null,
            'type' => $this->document_type,
            'path' => $path,
            'filename' => $filename,
            'original_filename' => $this->document->getClientOriginalName(),
            'mime_type' => $this->document->getMimeType(),
            'size_bytes' => $this->document->getSize(),
            'uploaded_by' => 'requestee',
        ]);

        $this->reset(['document', 'document_type']);
        $this->listingRequest->refresh();

        session()->flash('success', __('listing_requests.document_uploaded'));
    }

    public function download(RequestDocument $document): mixed
    {
        if ($document->listing_request_id !== $this->listingRequest->id) {
            abort(403);
        }

        return Storage::disk('private')->download($document->path, $document->original_filename);
    }

    public function render(): View
    {
        return view('livewire.listing-requests.requestee.documents')
            ->title($this->getTitle());
    }
}
