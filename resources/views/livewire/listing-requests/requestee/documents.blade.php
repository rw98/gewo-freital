<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listing-requests.portal', $listingRequest->access_token) }}">{{ __('listing_requests.portal_title') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('listing_requests.documents') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="success" icon="check-circle" dismissible>
                {{ session('success') }}
            </flux:callout>
        </div>
    @endif

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Upload Form --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('listing_requests.upload_document') }}</flux:heading>

                <form wire:submit="upload" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('listing_requests.document_type') }} *</flux:label>
                        <flux:select wire:model="document_type">
                            <flux:select.option value="">{{ __('Select type') }}</flux:select.option>
                            @foreach ($this->documentTypes as $type)
                                <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="document_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('File') }} *</flux:label>
                        <div
                            x-data="{ isDragging: false }"
                            x-on:dragover.prevent="isDragging = true"
                            x-on:dragleave.prevent="isDragging = false"
                            x-on:drop.prevent="isDragging = false"
                            class="relative"
                        >
                            <input
                                type="file"
                                wire:model="document"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            />
                            <div
                                class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                                :class="isDragging ? 'border-accent bg-accent/5' : 'border-gewo-grey-300 hover:border-gewo-grey-400'"
                            >
                                <flux:icon name="cloud-arrow-up" class="size-12 mx-auto text-gewo-grey-400 mb-3" />
                                <flux:text class="font-medium">{{ __('Drag and drop or click to upload') }}</flux:text>
                                <flux:text size="sm" class="text-gewo-grey-500 mt-1">PDF, JPG, PNG (max. 10 MB)</flux:text>

                                @if ($document)
                                    <div class="mt-4 p-3 bg-green-50 rounded-lg text-green-800 text-sm">
                                        <flux:icon name="document" class="size-4 inline" />
                                        {{ $document->getClientOriginalName() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <flux:error name="document" />
                    </flux:field>

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" icon="arrow-up-tray">
                            {{ __('listing_requests.upload_document') }}
                        </flux:button>
                    </div>
                </form>
            </flux:card>

            {{-- Existing Documents --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('listing_requests.documents') }}</flux:heading>

                @if ($listingRequest->documents->isEmpty())
                    <div class="text-center py-8">
                        <flux:icon name="document" class="size-12 mx-auto text-gewo-grey-300 mb-3" />
                        <flux:text class="text-gewo-grey-500">{{ __('listing_requests.no_documents') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($listingRequest->documents as $document)
                            <div class="flex items-center justify-between p-4 bg-gewo-grey-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gewo-grey-200">
                                        @if (str_contains($document->mime_type, 'pdf'))
                                            <flux:icon name="document-text" class="size-5 text-red-500" />
                                        @else
                                            <flux:icon name="photo" class="size-5 text-blue-500" />
                                        @endif
                                    </div>
                                    <div>
                                        <flux:text class="font-medium">{{ $document->original_filename }}</flux:text>
                                        <div class="flex items-center gap-2 text-sm text-gewo-grey-500">
                                            <flux:badge size="sm" variant="outline">{{ $document->type->label() }}</flux:badge>
                                            <span>&bull;</span>
                                            <span>{{ $document->humanReadableSize() }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $document->created_at->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <flux:button
                                    wire:click="download('{{ $document->id }}')"
                                    variant="ghost"
                                    size="sm"
                                    icon="arrow-down-tray"
                                >
                                    {{ __('listing_requests.download_document') }}
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Back Button --}}
            <div class="flex justify-start">
                <flux:button href="{{ route('listing-requests.portal', $listingRequest->access_token) }}" variant="ghost" icon="arrow-left" wire:navigate>
                    {{ __('Back to overview') }}
                </flux:button>
            </div>
        </div>
    </section>
</div>
