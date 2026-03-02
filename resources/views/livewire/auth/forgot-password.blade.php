<x-layouts::auth.public :title="__('pages.auth.forgot_password.title')">
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
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gewo-blue-100">
                        <flux:icon name="key" class="size-6 text-accent" />
                    </div>
                    <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.forgot_password.heading') }}</flux:heading>
                    <flux:text class="mt-2 text-gewo-grey-600">{{ __('pages.auth.forgot_password.description') }}</flux:text>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <flux:field>
                        <flux:label>{{ __('pages.auth.forgot_password.email') }}</flux:label>
                        <flux:input
                            name="email"
                            type="email"
                            required
                            autofocus
                            :placeholder="__('pages.auth.forgot_password.email_placeholder')"
                        />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                        {{ __('pages.auth.forgot_password.submit') }}
                    </flux:button>
                </form>

                <div class="mt-6 text-center">
                    <flux:link :href="route('login')" wire:navigate class="inline-flex items-center gap-1 text-sm">
                        <flux:icon name="arrow-left" class="size-4" />
                        {{ __('pages.auth.forgot_password.back_to_login') }}
                    </flux:link>
                </div>
            </flux:card>
        </div>
    </section>
</x-layouts::auth.public>
