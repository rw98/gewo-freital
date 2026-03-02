<?php

use App\Enums\ImageType;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public Model $model;

    public string $modelType;

    public string $modelId;

    #[Validate(['photos.*' => 'image|max:10240'])]
    public array $photos = [];

    public string $imageType = 'other';

    public function mount(Model $model): void
    {
        $this->model = $model;
        $this->modelType = get_class($model);
        $this->modelId = $model->id;
    }

    public function uploadPhotos(): void
    {
        $this->validate();

        foreach ($this->photos as $photo) {
            $path = $photo->store('images/'.$this->model->getTable(), 'public');

            $this->model->images()->create([
                'type' => $this->imageType,
                'path' => $path,
                'filename' => $photo->getClientOriginalName(),
                'order' => $this->model->images()->count(),
            ]);
        }

        $this->photos = [];
        $this->imageType = 'other';

        $this->dispatch('images-updated');
    }

    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function deleteImage(string $imageId): void
    {
        $image = Image::find($imageId);

        if ($image && $image->imageable_id === $this->model->id) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
            $image->delete();
            $this->dispatch('images-updated');
        }
    }

    public function getImageTypesProperty(): array
    {
        return ImageType::cases();
    }

    public function getExistingImagesProperty()
    {
        return $this->model->images()->orderBy('order')->get();
    }
};
?>

<div class="space-y-6" wire:poll.5s="$refresh">
    {{-- Existing Images --}}
    @if($this->existingImages->count() > 0)
        <div>
            <flux:heading size="sm" class="mb-3">{{ __('Uploaded Images') }}</flux:heading>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($this->existingImages as $image)
                    <div class="group relative overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <img
                            src="{{ $image->url() }}"
                            alt="{{ $image->filename }}"
                            class="aspect-square w-full object-cover"
                        />
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                            <flux:button
                                wire:click="deleteImage('{{ $image->id }}')"
                                wire:confirm="{{ __('Are you sure you want to delete this image?') }}"
                                variant="danger"
                                size="sm"
                                icon="trash"
                            />
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 px-2 py-1">
                            <span class="text-xs text-white">{{ $image->type->label() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Upload Form --}}
    <div>
        <flux:heading size="sm" class="mb-3">{{ __('Upload New Images') }}</flux:heading>

        <div class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Image Type') }}</flux:label>
                <flux:select wire:model="imageType">
                    @foreach($this->imageTypes as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:file-upload wire:model="photos" multiple label="{{ __('Select Images') }}">
                <flux:file-upload.dropzone
                    heading="{{ __('Drop images here or click to browse') }}"
                    text="{{ __('JPG, PNG, GIF up to 10MB') }}"
                />
            </flux:file-upload>

            @if(count($photos) > 0)
                <div class="flex flex-col gap-2">
                    @foreach($photos as $index => $photo)
                        <flux:file-item
                            :heading="$photo->getClientOriginalName()"
                            :image="$photo->temporaryUrl()"
                            :size="$photo->getSize()"
                        >
                            <x-slot name="actions">
                                <flux:file-item.remove
                                    wire:click="removePhoto({{ $index }})"
                                    aria-label="{{ __('Remove file') }}: {{ $photo->getClientOriginalName() }}"
                                />
                            </x-slot>
                        </flux:file-item>
                    @endforeach
                </div>

                <flux:button wire:click="uploadPhotos" variant="primary" icon="arrow-up-tray">
                    {{ __('Upload Images') }}
                </flux:button>
            @endif
        </div>
    </div>
</div>
