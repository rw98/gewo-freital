<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:field>
                <flux:label>{{ __('Salutation') }}</flux:label>
                <flux:select wire:model="salutation">
                    <flux:select.option value="">{{ __('None') }}</flux:select.option>
                    <flux:select.option value="Herr">{{ __('Mr.') }}</flux:select.option>
                    <flux:select.option value="Frau">{{ __('Ms.') }}</flux:select.option>
                    <flux:select.option value="Dr.">{{ __('Dr.') }}</flux:select.option>
                    <flux:select.option value="Prof.">{{ __('Prof.') }}</flux:select.option>
                </flux:select>
                <flux:error name="salutation" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('First name') }}</flux:label>
                    <flux:input wire:model="first_name" type="text" required autofocus autocomplete="given-name" />
                    <flux:error name="first_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Last name') }}</flux:label>
                    <flux:input wire:model="last_name" type="text" required autocomplete="family-name" />
                    <flux:error name="last_name" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Middle name') }}</flux:label>
                <flux:input wire:model="middle_name" type="text" autocomplete="additional-name" />
                <flux:error name="middle_name" />
            </flux:field>

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
