<?php

namespace App\Livewire;

use App\Models\NavItem;
use App\Models\Post;
use App\Models\Setting;
use Livewire\Component;

class BlogDetail extends Component
{
    public $slug;

    public function mount($slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $post = Post::where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Get recent posts for "Read More" section
        $recentPosts = Post::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

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

        return view('livewire.blog-detail', [
            'post' => $post,
            'recentPosts' => $recentPosts,
            'settings' => $settings,
            'navItems' => $navItems,
        ])->layout('components.layouts.guest');
    }
}
