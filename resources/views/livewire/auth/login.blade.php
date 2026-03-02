<x-layouts::auth.public :title="__('pages.auth.login.title')">
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
                    <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.login.heading') }}</flux:heading>
                    <flux:text class="mt-2 text-gewo-grey-600">{{ __('pages.auth.login.description') }}</flux:text>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <flux:field>
                        <flux:label>{{ __('pages.auth.login.email') }}</flux:label>
                        <flux:input
                            name="email"
                            :value="old('email')"
                            type="email"
                            required
                            autofocus
                            autocomplete="email"
                            :placeholder="__('pages.auth.login.email_placeholder')"
                        />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center justify-between">
                            <flux:label>{{ __('pages.auth.login.password') }}</flux:label>
                            @if (Route::has('password.request'))
                                <flux:link :href="route('password.request')" class="text-sm" wire:navigate>
                                    {{ __('pages.auth.login.forgot_password') }}
                                </flux:link>
                            @endif
                        </div>
                        <flux:input
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('pages.auth.login.password_placeholder')"
                            viewable
                        />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:checkbox name="remember" :label="__('pages.auth.login.remember_me')" :checked="old('remember')" />
                    </flux:field>

                    <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                        {{ __('pages.auth.login.submit') }}
                    </flux:button>
                </form>

            </flux:card>

            <div class="mt-8 text-center">
                <flux:text size="sm" class="text-gewo-grey-500">
                    {{ __('pages.auth.login.security_note') }}
                </flux:text>
                <div class="mt-2 flex items-center justify-center gap-4">
                    <flux:icon name="shield-check" class="size-5 text-gewo-grey-400" />
                    <flux:icon name="lock-closed" class="size-5 text-gewo-grey-400" />
                    <flux:icon name="check-badge" class="size-5 text-gewo-grey-400" />
                </div>
            </div>
        </div>
    </section>
</x-layouts::auth.public>
