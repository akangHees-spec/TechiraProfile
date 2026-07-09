<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Service;
use App\Models\Setting;
use Livewire\Component;

class ServiceList extends Component
{
    public function render()
    {
        $services = Service::where('is_active', true)
            ->with(['category', 'features'])
            ->orderBy('order')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('order')->get();

        $settingsRaw = Setting::all()->pluck('value', 'key');
        $settings = [
            'company_name' => $settingsRaw['company_name'] ?? 'Techira Nusantara',
            'logo' => $settingsRaw['logo'] ?? null,
            'social_facebook' => $settingsRaw['social_facebook'] ?? '#',
            'social_instagram' => $settingsRaw['social_instagram'] ?? '#',
            'social_linkedin' => $settingsRaw['social_linkedin'] ?? '#',
            'youtube_url' => $settingsRaw['youtube_url'] ?? '#',
        ];

        return view('livewire.service-list', [
            'services' => $services,
            'categories' => $categories,
            'settings' => $settings,
        ])->layout('components.layouts.guest');
    }
}
