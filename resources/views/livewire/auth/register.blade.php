<x-layouts::auth.public :title="__('pages.auth.register.title')">
    <section class="relative bg-linear-to-br from-gewo-blue-50 to-white py-16 lg:py-24">
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1" fill="#00a3d9"/>
                </pattern>
                <rect width="100" height="100" fill="url(#dots)"/>
            </svg>
        </div>

        <div class="relative mx-auto max-w-md px-4 sm:px-6">
            <flux:card class="p-8">
                <div class="text-center mb-8">
                    <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.register.heading') }}</flux:heading>
                    <flux:text class="mt-2 text-gewo-grey-600">{{ __('pages.auth.register.description') }}</flux:text>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                    @csrf

                    <flux:field>
                        <flux:label>{{ __('pages.auth.register.name') }}</flux:label>
                        <flux:input
                            name="name"
                            :value="old('name')"
                            type="text"
                            required
                            autofocus
                            autocomplete="name"
                            :placeholder="__('pages.auth.register.name_placeholder')"
                        />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('pages.auth.register.email') }}</flux:label>
                        <flux:input
                            name="email"
                            :value="old('email')"
                            type="email"
                            required
                            autocomplete="email"
                            :placeholder="__('pages.auth.register.email_placeholder')"
                        />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('pages.auth.register.password') }}</flux:label>
                        <flux:input
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            :placeholder="__('pages.auth.register.password_placeholder')"
                            viewable
                        />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('pages.auth.register.password_confirm') }}</flux:label>
                        <flux:input
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            :placeholder="__('pages.auth.register.password_confirm_placeholder')"
                            viewable
                        />
                        <flux:error name="password_confirmation" />
                    </flux:field>

                    <flux:button variant="primary" type="submit" class="w-full" data-test="register-user-button">
                        {{ __('pages.auth.register.submit') }}
                    </flux:button>
                </form>

                <div class="mt-6 text-center">
                    <flux:text size="sm" class="text-gewo-grey-600">
                        {{ __('pages.auth.register.has_account') }}
                        <flux:link :href="route('login')" wire:navigate class="font-medium">{{ __('pages.auth.register.sign_in') }}</flux:link>
                    </flux:text>
                </div>
            </flux:card>

            <div class="mt-8 text-center">
                <flux:text size="sm" class="text-gewo-grey-500">
                    {{ __('pages.auth.register.terms_note') }}
                </flux:text>
            </div>
        </div>
    </section>
</x-layouts::auth.public>
