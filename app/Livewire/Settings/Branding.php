<?php

namespace App\Livewire\Settings;

use App\Models\GlobalSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Branding-Einstellungen')]
class Branding extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public ?string $logoUrl = null;

    public ?string $faviconUrl = null;

    public ?string $siteName = null;

    public $logoUpload = null;

    public $faviconUpload = null;

    public function mount(): void
    {
        $this->authorize('manage-pages');

        $settings = GlobalSetting::getValue('branding', []);
        $this->logoUrl = $settings['logo_url'] ?? null;
        $this->faviconUrl = $settings['favicon_url'] ?? null;
        $this->siteName = $settings['site_name'] ?? config('app.name');
    }

    public function updatedLogoUpload(): void
    {
        $this->validate([
            'logoUpload' => ['image', 'max:2048', 'mimes:png,jpg,jpeg,svg,webp'],
        ]);

        $path = $this->logoUpload->store('branding', 'public');
        $this->logoUrl = Storage::disk('public')->url($path);
        $this->save();
        $this->logoUpload = null;
    }

    public function updatedFaviconUpload(): void
    {
        $this->validate([
            'faviconUpload' => ['image', 'max:512', 'mimes:ico,png,svg'],
        ]);

        $path = $this->faviconUpload->store('branding', 'public');
        $this->faviconUrl = Storage::disk('public')->url($path);
        $this->save();
        $this->faviconUpload = null;
    }

    public function removeLogo(): void
    {
        $this->logoUrl = null;
        $this->save();
    }

    public function removeFavicon(): void
    {
        $this->faviconUrl = null;
        $this->save();
    }

    public function updatedSiteName(): void
    {
        $this->save();
    }

    public function save(): void
    {
        GlobalSetting::setValue('branding', [
            'logo_url' => $this->logoUrl,
            'favicon_url' => $this->faviconUrl,
            'site_name' => $this->siteName,
        ]);

        $this->dispatch('branding-saved');
    }

    public function render(): View
    {
        return view('livewire.settings.branding');
    }
}
