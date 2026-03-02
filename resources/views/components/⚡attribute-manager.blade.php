<?php

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

new class extends Component
{
    public Model $model;

    public string $modelType;

    public string $modelId;

    public string $title = '';

    public string $description = '';

    public ?string $editingId = null;

    public function mount(Model $model): void
    {
        $this->model = $model;
        $this->modelType = get_class($model);
        $this->modelId = $model->id;
    }

    public function addAttribute(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $this->model->attributes()->create([
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->model->attributes()->count(),
        ]);

        $this->reset(['title', 'description']);
        $this->dispatch('attributes-updated');
    }

    public function editAttribute(string $id): void
    {
        $attribute = Attribute::find($id);

        if ($attribute && $attribute->attributable_id === $this->model->id) {
            $this->editingId = $id;
            $this->title = $attribute->title;
            $this->description = $attribute->description ?? '';
        }
    }

    public function updateAttribute(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $attribute = Attribute::find($this->editingId);

        if ($attribute && $attribute->attributable_id === $this->model->id) {
            $attribute->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);
        }

        $this->cancelEdit();
        $this->dispatch('attributes-updated');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset(['title', 'description']);
    }

    public function deleteAttribute(string $id): void
    {
        $attribute = Attribute::find($id);

        if ($attribute && $attribute->attributable_id === $this->model->id) {
            $attribute->delete();
            $this->dispatch('attributes-updated');
        }
    }

    public function getAttributesProperty()
    {
        return $this->model->attributes()->orderBy('order')->get();
    }
};
?>

<div class="space-y-6">
    {{-- Existing Attributes --}}
    @if($this->attributes->count() > 0)
        <div>
            <flux:heading size="sm" class="mb-3">{{ __('Existing Attributes') }}</flux:heading>
            <div class="space-y-3">
                @foreach($this->attributes as $attribute)
                    <div class="flex items-start justify-between gap-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700" wire:key="attr-{{ $attribute->id }}">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium">{{ $attribute->title }}</div>
                            @if($attribute->description)
                                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $attribute->description }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button
                                wire:click="editAttribute('{{ $attribute->id }}')"
                                variant="ghost"
                                size="sm"
                                icon="pencil"
                            />
                            <flux:button
                                wire:click="deleteAttribute('{{ $attribute->id }}')"
                                wire:confirm="{{ __('Are you sure you want to delete this attribute?') }}"
                                variant="ghost"
                                size="sm"
                                icon="trash"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Add/Edit Form --}}
    <div>
        <flux:heading size="sm" class="mb-3">
            {{ $editingId ? __('Edit Attribute') : __('Add New Attribute') }}
        </flux:heading>

        <form wire:submit="{{ $editingId ? 'updateAttribute' : 'addAttribute' }}" class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Title') }}</flux:label>
                <flux:input wire:model="title" placeholder="{{ __('e.g., Year built, Heating type') }}" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }} <span class="text-zinc-400">({{ __('optional') }})</span></flux:label>
                <flux:textarea wire:model="description" rows="2" placeholder="{{ __('Additional details...') }}" />
                <flux:error name="description" />
            </flux:field>

            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="primary" icon="{{ $editingId ? 'check' : 'plus' }}">
                    {{ $editingId ? __('Update') : __('Add Attribute') }}
                </flux:button>

                @if($editingId)
                    <flux:button type="button" wire:click="cancelEdit" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                @endif
            </div>
        </form>
    </div>
</div>
