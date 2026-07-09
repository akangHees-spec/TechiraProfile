<?php

namespace App\Livewire;

use App\Models\NavItem;
use App\Models\Service;
use App\Models\Setting;
use Livewire\Component;

class ServiceDetail extends Component
{
    public $service;

    public function mount($slug)
    {
        $this->service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'features'])
            ->firstOrFail();
    }

    public function trackWa()
    {
        $this->service->increment('whatsapp_click_count');
        $this->dispatch('redirect-to', url: $this->service->whatsapp_link);
    }

    public function render()
    {
        $settingsRaw = Setting::all()->pluck('value', 'key');
        $settings = [
            'company_name' => $settingsRaw['company_name'] ?? 'Techira Nusantara',
            'logo' => $settingsRaw['logo'] ?? null,
            'social_facebook' => $settingsRaw['social_facebook'] ?? '#',
            'social_instagram' => $settingsRaw['social_instagram'] ?? '#',
            'social_linkedin' => $settingsRaw['social_linkedin'] ?? '#',
            'youtube_url' => $settingsRaw['youtube_url'] ?? '#',
        ];

        $navItems = NavItem::where('is_active', true)->whereNull('parent_id')->orderBy('order')->get();

        return view('livewire.service-detail', [
            'settings' => $settings,
            'navItems' => $navItems,
        ])->layout('components.layouts.guest');
    }
}
