<x-layouts::auth.public :title="__('pages.auth.verify_email.title')">
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
                        <flux:icon name="envelope" class="size-6 text-accent" />
                    </div>
                    <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.verify_email.heading') }}</flux:heading>
                    <flux:text class="mt-2 text-gewo-grey-600">
                        {{ __('pages.auth.verify_email.description') }}
                    </flux:text>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <flux:callout color="emerald" class="mb-6">
                        <flux:callout.text>
                            {{ __('pages.auth.verify_email.resent') }}
                        </flux:callout.text>
                    </flux:callout>
                @endif

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('pages.auth.verify_email.resend') }}
                        </flux:button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button variant="ghost" type="submit" class="w-full" data-test="logout-button">
                            {{ __('pages.auth.verify_email.logout') }}
                        </flux:button>
                    </form>
                </div>
            </flux:card>

            <div class="mt-8 text-center">
                <flux:text size="sm" class="text-gewo-grey-500">
                    {{ __('pages.auth.verify_email.help_note') }}
                </flux:text>
            </div>
        </div>
    </section>
</x-layouts::auth.public>
