<x-layouts::auth.public :title="__('pages.auth.confirm_password.title')">
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
                        <flux:icon name="shield-check" class="size-6 text-accent" />
                    </div>
                    <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.confirm_password.heading') }}</flux:heading>
                    <flux:text class="mt-2 text-gewo-grey-600">
                        {{ __('pages.auth.confirm_password.description') }}
                    </flux:text>
                </div>

                <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-5">
                    @csrf

                    <flux:field>
                        <flux:label>{{ __('pages.auth.confirm_password.password') }}</flux:label>
                        <flux:input
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('pages.auth.confirm_password.password_placeholder')"
                            viewable
                        />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                        {{ __('pages.auth.confirm_password.submit') }}
                    </flux:button>
                </form>
            </flux:card>
        </div>
    </section>
</x-layouts::auth.public>
