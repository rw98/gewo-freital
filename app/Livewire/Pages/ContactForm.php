<?php

namespace App\Livewire\Pages;

use App\Models\PageBlock;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    public PageBlock $block;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    public bool $submitted = false;

    public function mount(PageBlock $block): void
    {
        $this->block = $block;
    }

    public function submit(): void
    {
        $this->validate();

        $recipientEmail = $this->block->getContent('recipient_email', config('mail.from.address'));

        if ($recipientEmail) {
            Mail::raw($this->message, function ($mail) use ($recipientEmail) {
                $mail->to($recipientEmail)
                    ->from($this->email, $this->name)
                    ->replyTo($this->email, $this->name)
                    ->subject($this->subject);
            });
        }

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.pages.contact-form');
    }
}
