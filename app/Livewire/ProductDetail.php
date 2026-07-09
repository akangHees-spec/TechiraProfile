<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Setting;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();
    }

    public function trackWa()
    {
        $this->product->increment('whatsapp_click_count');
        $this->dispatch('redirect-to', url: $this->product->whatsapp_link);
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

        return view('livewire.product-detail', [
            'settings' => $settings,
        ])->layout('components.layouts.guest');
    }
}
