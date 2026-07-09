<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Livewire\Component;

class ProductList extends Component
{
    public function render()
    {
        $products = Product::where('is_active', true)
            ->with('category')
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

        return view('livewire.product-list', [
            'products' => $products,
            'categories' => $categories,
            'settings' => $settings,
        ])->layout('components.layouts.guest');
    }
}
