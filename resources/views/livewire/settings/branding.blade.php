<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('pages.branding.heading')" :subheading="__('pages.branding.subheading')">
        <div class="my-6 space-y-8">
            {{-- Site Name --}}
            <flux:field>
                <flux:label>{{ __('pages.branding.site_name') }}</flux:label>
                <flux:input wire:model.blur="siteName" placeholder="{{ config('app.name') }}" />
                <flux:description>{{ __('pages.branding.site_name_description') }}</flux:description>
            </flux:field>

            <flux:separator />

            {{-- Logo Upload --}}
            <div>
                <flux:heading size="sm" class="mb-3">{{ __('pages.branding.logo') }}</flux:heading>
                <flux:description class="mb-4">{{ __('pages.branding.logo_description') }}</flux:description>

                @if ($logoUrl)
                    <div class="mb-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <img src="{{ $logoUrl }}" alt="Logo" class="h-12 max-w-[200px] object-contain" />
                                <flux:text size="sm" class="text-zinc-500">{{ __('pages.branding.current_logo') }}</flux:text>
                            </div>
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="removeLogo" wire:confirm="{{ __('pages.branding.confirm_remove_logo') }}">
                                {{ __('pages.branding.remove') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <div
                    x-data="{ isDragging: false }"
                    x-on:dragover.prevent="isDragging = true"
                    x-on:dragleave.prevent="isDragging = false"
                    x-on:drop.prevent="isDragging = false"
                    class="relative"
                >
                    <label
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                        :class="isDragging ? 'border-accent bg-accent/5' : 'border-zinc-300 dark:border-zinc-600 hover:border-accent hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    >
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <flux:icon name="arrow-up-tray" class="size-8 mb-2 text-zinc-400" />
                            <flux:text size="sm">{{ __('pages.branding.upload_logo') }}</flux:text>
                            <flux:text size="xs" class="text-zinc-500 mt-1">PNG, JPG, SVG, WebP (max 2MB)</flux:text>
                        </div>
                        <input type="file" wire:model="logoUpload" class="hidden" accept="image/png,image/jpeg,image/svg+xml,image/webp" />
                    </label>

                    <div wire:loading wire:target="logoUpload" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-800/80 rounded-lg">
                        <flux:icon name="arrow-path" class="size-6 animate-spin text-accent" />
                    </div>
                </div>

                @error('logoUpload')
                    <flux:text size="sm" class="text-red-500 mt-2">{{ $message }}</flux:text>
                @enderror
            </div>

            <flux:separator />

            {{-- Favicon Upload --}}
            <div>
                <flux:heading size="sm" class="mb-3">{{ __('pages.branding.favicon') }}</flux:heading>
                <flux:description class="mb-4">{{ __('pages.branding.favicon_description') }}</flux:description>

                @if ($faviconUrl)
                    <div class="mb-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <img src="{{ $faviconUrl }}" alt="Favicon" class="size-8 object-contain" />
                                <flux:text size="sm" class="text-zinc-500">{{ __('pages.branding.current_favicon') }}</flux:text>
                            </div>
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="removeFavicon" wire:confirm="{{ __('pages.branding.confirm_remove_favicon') }}">
                                {{ __('pages.branding.remove') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <div
                    x-data="{ isDragging: false }"
                    x-on:dragover.prevent="isDragging = true"
                    x-on:dragleave.prevent="isDragging = false"
                    x-on:drop.prevent="isDragging = false"
                    class="relative"
                >
                    <label
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                        :class="isDragging ? 'border-accent bg-accent/5' : 'border-zinc-300 dark:border-zinc-600 hover:border-accent hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    >
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <flux:icon name="arrow-up-tray" class="size-8 mb-2 text-zinc-400" />
                            <flux:text size="sm">{{ __('pages.branding.upload_favicon') }}</flux:text>
                            <flux:text size="xs" class="text-zinc-500 mt-1">ICO, PNG, SVG (max 512KB)</flux:text>
                        </div>
                        <input type="file" wire:model="faviconUpload" class="hidden" accept="image/x-icon,image/png,image/svg+xml" />
                    </label>

                    <div wire:loading wire:target="faviconUpload" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-800/80 rounded-lg">
                        <flux:icon name="arrow-path" class="size-6 animate-spin text-accent" />
                    </div>
                </div>

                @error('faviconUpload')
                    <flux:text size="sm" class="text-red-500 mt-2">{{ $message }}</flux:text>
                @enderror
            </div>

            <x-action-message on="branding-saved">
                {{ __('pages.branding.saved') }}
            </x-action-message>
        </div>
    </x-settings.layout>
</section>
