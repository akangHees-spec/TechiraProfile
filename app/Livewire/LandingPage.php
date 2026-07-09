<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\NavItem;
use App\Models\PageSection;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LandingPage extends Component
{
    // Contact form fields
    public $name = '';

    public $email = '';

    public $phone = '';

    public $subject = '';

    public $message = '';

    public function submitContact(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ];

        $this->validate($rules);

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->subject = '';
        $this->message = '';

        session()->flash('contact_toast', 'Pesan Anda berhasil dikirim! Tim kami akan segera menghubungi Anda.');
    }

    public function trackProductWa(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->increment('whatsapp_click_count');

        $this->dispatch('redirect-to', url: $product->whatsapp_link);
    }

    public function trackServiceWa(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->increment('whatsapp_click_count');

        $this->dispatch('redirect-to', url: $service->whatsapp_link);
    }

    public function render(): View
    {
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        $partners = Partner::where('is_active', true)->orderBy('order')->get();
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('order')
            ->get();

        $featuredServices = Service::where('is_active', true)
            ->where('is_featured', true)
            ->with(['category', 'features'])
            ->orderBy('order')
            ->get();

        $sections = PageSection::where('is_active', true)->orderBy('order')->get()->keyBy('section_key');
        $team = TeamMember::where('is_active', true)->orderBy('order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();
        $navItems = NavItem::where('is_active', true)->whereNull('parent_id')->orderBy('order')->get();

        // Get global settings for landing page footer/contacts
        $settingsRaw = Setting::all()->pluck('value', 'key');
        $settings = [
            'company_name' => $settingsRaw['company_name'] ?? 'Techira Nusantara',
            'phone' => $settingsRaw['phone'] ?? '',
            'email' => $settingsRaw['email'] ?? '',
            'address' => $settingsRaw['address'] ?? '',
            'whatsapp_number' => $settingsRaw['whatsapp_number'] ?? '',
            'google_maps_embed' => $settingsRaw['google_maps_embed'] ?? '',
            'social_facebook' => $settingsRaw['social_facebook'] ?? '#',
            'social_instagram' => $settingsRaw['social_instagram'] ?? '#',
            'social_linkedin' => $settingsRaw['social_linkedin'] ?? '#',
            'youtube_url' => $settingsRaw['youtube_url'] ?? '#',
            'logo' => $settingsRaw['logo'] ?? null,
            'whatsapp_message_template' => $settingsRaw['whatsapp_message_template'] ?? 'Halo Techira Nusantara, saya tertarik dengan layanan Anda.',
        ];

        return view('livewire.landing-page', [
            'sliders' => $sliders,
            'partners' => $partners,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'featuredServices' => $featuredServices,
            'sections' => $sections,
            'team' => $team,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'settings' => $settings,
            'navItems' => $navItems,
        ]);
    }
}
