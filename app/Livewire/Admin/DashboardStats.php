<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Service;
use App\Models\ContactMessage;

class DashboardStats extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        $totalProducts = Product::count();
        $totalServices = Service::count();
        $unreadMessages = ContactMessage::where('is_read', false)->count();
        
        $totalWaClicks = Product::sum('whatsapp_click_count') + Service::sum('whatsapp_click_count');

        // Top 5 Products and Services based on WhatsApp click counts
        $topProducts = Product::select('id', 'name', 'whatsapp_click_count', 'category_id')
            ->with('category')
            ->orderBy('whatsapp_click_count', 'desc')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'clicks' => $item->whatsapp_click_count,
                'type' => 'Produk',
                'category' => $item->category?->name ?? 'N/A'
            ]);

        $topServices = Service::select('id', 'name', 'whatsapp_click_count', 'category_id')
            ->with('category')
            ->orderBy('whatsapp_click_count', 'desc')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'clicks' => $item->whatsapp_click_count,
                'type' => 'Jasa',
                'category' => $item->category?->name ?? 'N/A'
            ]);

        $topAsked = $topProducts->concat($topServices)
            ->sortByDesc('clicks')
            ->take(5)
            ->values()
            ->all();

        return view('livewire.admin.dashboard-stats', [
            'totalProducts' => $totalProducts,
            'totalServices' => $totalServices,
            'unreadMessages' => $unreadMessages,
            'totalWaClicks' => $totalWaClicks,
            'topAsked' => $topAsked,
        ]);
    }
}
