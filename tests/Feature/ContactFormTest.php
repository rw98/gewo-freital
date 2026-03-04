<?php

use App\Enums\BlockType;
use App\Livewire\Pages\ContactForm;
use App\Models\PageBlock;
use Livewire\Livewire;

it('renders the contact form', function () {
    $block = PageBlock::factory()
        ->ofType(BlockType::ContactForm)
        ->create();

    Livewire::test(ContactForm::class, ['block' => $block])
        ->assertSee(__('pages.blocks.contact_form.name'))
        ->assertSee(__('pages.blocks.contact_form.email'))
        ->assertSee(__('pages.blocks.contact_form.subject'))
        ->assertSee(__('pages.blocks.contact_form.message'));
});

it('validates required fields', function () {
    $block = PageBlock::factory()
        ->ofType(BlockType::ContactForm)
        ->create();

    Livewire::test(ContactForm::class, ['block' => $block])
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'message']);
});

it('validates email format', function () {
    $block = PageBlock::factory()
        ->ofType(BlockType::ContactForm)
        ->create();

    Livewire::test(ContactForm::class, ['block' => $block])
        ->set('name', 'John Doe')
        ->set('email', 'invalid-email')
        ->set('subject', 'Test Subject')
        ->set('message', 'Test message content')
        ->call('submit')
        ->assertHasErrors(['email']);
});

it('submits form successfully with valid data', function () {
    $block = PageBlock::factory()
        ->ofType(BlockType::ContactForm)
        ->create([
            'content' => [
                'recipient_email' => 'test@example.com',
                'success_message' => 'Thank you for your message!',
            ],
        ]);

    Livewire::test(ContactForm::class, ['block' => $block])
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('subject', 'Test Subject')
        ->set('message', 'This is a test message')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true)
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('subject', '')
        ->assertSet('message', '');
});

it('shows success message after submission', function () {
    $successMessage = 'Your message has been sent!';

    $block = PageBlock::factory()
        ->ofType(BlockType::ContactForm)
        ->create([
            'content' => [
                'recipient_email' => 'test@example.com',
                'success_message' => $successMessage,
            ],
        ]);

    Livewire::test(ContactForm::class, ['block' => $block])
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('subject', 'Test Subject')
        ->set('message', 'This is a test message')
        ->call('submit')
        ->assertSee($successMessage);
});
