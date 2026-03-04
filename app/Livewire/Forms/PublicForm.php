<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth.public')]
class PublicForm extends Component
{
    public Form $form;

    public function mount(string $key): void
    {
        $this->form = Form::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();
    }

    public function getTitle(): string
    {
        return $this->form->name;
    }

    public function render(): View
    {
        return view('livewire.forms.public-form')
            ->title($this->getTitle());
    }
}
