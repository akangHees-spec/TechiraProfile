<?php

namespace App\Livewire;

use App\Models\NavItem;
use App\Models\Post;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Post::where('is_published', true);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('content', 'like', '%'.$this->search.'%');
            });
        }

        $posts = $query->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(9);

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

        return view('livewire.blog-index', [
            'posts' => $posts,
            'settings' => $settings,
            'navItems' => $navItems,
        ])->layout('components.layouts.guest');
    }
}
