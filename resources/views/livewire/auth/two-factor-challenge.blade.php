<x-layouts::auth.public :title="__('pages.auth.two_factor.title')">
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
                <div
                    x-cloak
                    x-data="{
                        showRecoveryInput: @js($errors->has('recovery_code')),
                        code: '',
                        recovery_code: '',
                        toggleInput() {
                            this.showRecoveryInput = !this.showRecoveryInput;
                            this.code = '';
                            this.recovery_code = '';
                            $dispatch('clear-2fa-auth-code');
                            $nextTick(() => {
                                this.showRecoveryInput
                                    ? this.$refs.recovery_code?.focus()
                                    : $dispatch('focus-2fa-auth-code');
                            });
                        },
                    }"
                >
                    <div class="text-center mb-8">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gewo-blue-100">
                            <flux:icon name="device-phone-mobile" class="size-6 text-accent" />
                        </div>

                        <div x-show="!showRecoveryInput">
                            <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.two_factor.code_heading') }}</flux:heading>
                            <flux:text class="mt-2 text-gewo-grey-600">
                                {{ __('pages.auth.two_factor.code_description') }}
                            </flux:text>
                        </div>

                        <div x-show="showRecoveryInput">
                            <flux:heading size="xl" class="text-gewo-grey-900">{{ __('pages.auth.two_factor.recovery_heading') }}</flux:heading>
                            <flux:text class="mt-2 text-gewo-grey-600">
                                {{ __('pages.auth.two_factor.recovery_description') }}
                            </flux:text>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('two-factor.login.store') }}" class="space-y-5">
                        @csrf

                        <div x-show="!showRecoveryInput">
                            <div class="flex items-center justify-center">
                                <flux:otp
                                    x-model="code"
                                    length="6"
                                    name="code"
                                    label="OTP Code"
                                    label:sr-only
                                    class="mx-auto"
                                />
                            </div>
                            <flux:error name="code" class="mt-2 text-center" />
                        </div>

                        <div x-show="showRecoveryInput">
                            <flux:field>
                                <flux:label>{{ __('pages.auth.two_factor.recovery_label') }}</flux:label>
                                <flux:input
                                    type="text"
                                    name="recovery_code"
                                    x-ref="recovery_code"
                                    x-bind:required="showRecoveryInput"
                                    autocomplete="one-time-code"
                                    x-model="recovery_code"
                                    placeholder="XXXX-XXXX"
                                />
                                <flux:error name="recovery_code" />
                            </flux:field>
                        </div>

                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('pages.auth.two_factor.submit') }}
                        </flux:button>

                        <div class="text-center">
                            <flux:link @click="toggleInput()" class="text-sm cursor-pointer">
                                <span x-show="!showRecoveryInput">{{ __('pages.auth.two_factor.use_recovery') }}</span>
                                <span x-show="showRecoveryInput">{{ __('pages.auth.two_factor.use_code') }}</span>
                            </flux:link>
                        </div>
                    </form>
                </div>
            </flux:card>

            <div class="mt-8 text-center">
                <flux:text size="sm" class="text-gewo-grey-500">
                    {{ __('pages.auth.two_factor.help_note') }}
                </flux:text>
            </div>
        </div>
    </section>
</x-layouts::auth.public>
