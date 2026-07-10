<?php

use Livewire\Component;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\PageSection;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\ContactMessage;
use App\Models\Setting;

new class extends Component
{
    // Contact form fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';

    public function submitContact()
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

    public function trackProductWa($id)
    {
        $product = Product::findOrFail($id);
        $product->increment('whatsapp_click_count');
        
        $this->dispatch('redirect-to', url: $product->whatsapp_link);
    }

    public function trackServiceWa($id)
    {
        $service = Service::findOrFail($id);
        $service->increment('whatsapp_click_count');

        $this->dispatch('redirect-to', url: $service->whatsapp_link);
    }

    public function render()
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

        $sections = PageSection::where('is_active', true)->get()->keyBy('section_key');
        $team = TeamMember::where('is_active', true)->orderBy('order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();

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

        return view('components.⚡landing-page', [
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
        ]);
    }
};
?>

<div 
    x-data="{ scrolled: false }" 
    @scroll.window="scrolled = (window.pageYOffset > 50)"
    class="min-h-screen bg-white text-slate-800 selection:bg-accent selection:text-white"
    x-init="
        window.addEventListener('redirect-to', e => {
            window.open(e.detail.url, '_blank');
        });
    "
>
    <!-- Toast Notification (Contact Success) -->
    @if (session()->has('contact_toast'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 w-full max-w-sm p-4 bg-white border border-slate-200 rounded-lg shadow-xl"
        >
            <div class="flex-shrink-0">
                <x-lucide-check-circle class="w-6 h-6 text-success" />
            </div>
            <div class="flex-1 text-sm font-medium text-slate-900">
                {{ session('contact_toast') }}
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- 1. Navbar -->
    <x-site-navbar :settings="$settings" :navItems="$navItems" :isLanding="true" />

    <!-- 2. Hero Section (with Slider Carousel) -->
    <section class="relative bg-primary text-white pt-32 pb-24 md:pt-48 md:pb-36 overflow-hidden">
        
        <!-- Subtle Grid Overlay -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-35"></div>

        <!-- Slider Carousel (Alpine.js) -->
        <div x-data="{ activeSlide: 0, maxSlide: {{ count($sliders) - 1 }} }" class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="relative min-h-[300px] md:min-h-[380px] flex items-center">
                @foreach ($sliders as $index => $slider)
                    <div 
                        x-show="activeSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-500 transform"
                        x-transition:enter-start="opacity-0 translate-x-12"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300 transform absolute"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-12"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full"
                        style="{{ $index > 0 ? 'display: none;' : '' }}"
                    >
                        <!-- Copywriting Content -->
                        <div class="space-y-6">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight font-sans">
                                {{ $slider->title }}
                            </h1>
                            <p class="text-base md:text-lg text-slate-300 leading-relaxed max-w-xl">
                                {{ $slider->subtitle }}
                            </p>
                            @if ($slider->button_text)
                                <div class="pt-4 flex flex-wrap gap-4">
                                    <a href="{{ $slider->button_link }}" class="px-6 py-3 bg-accent hover:bg-accent/90 text-white font-semibold rounded-lg shadow-sm transition-colors text-sm">
                                        {{ $slider->button_text }}
                                    </a>
                                    <a href="#services" class="px-6 py-3 border border-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors text-sm">
                                        Lihat Layanan
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Hero Flat Visual Mockup -->
                        <div class="relative flex justify-center">
                            @if ($slider->getFirstMediaUrl('image'))
                                <div class="w-full max-w-lg aspect-[16/10] rounded-xl overflow-hidden shadow-2xl border border-slate-800 bg-slate-900/50 p-2">
                                    <img src="{{ $slider->getFirstMediaUrl('image') }}" class="w-full h-full object-cover rounded-lg" />
                                </div>
                            @else
                                <!-- High-quality Flat Graphic Box (Bukan slop AI) -->
                                <div class="w-full max-w-lg aspect-[16/10] bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-2xl flex flex-col justify-between">
                                    <div class="flex items-center gap-2 text-slate-500 font-mono text-xs">
                                        <span class="w-3 h-3 bg-danger rounded-full"></span>
                                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                        <span class="w-3 h-3 bg-success rounded-full"></span>
                                        <span class="ml-2">techira-console-v1.log</span>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center font-mono text-sm text-accent space-y-2 mt-4">
                                        <p class="text-white">$ npm run build --prod</p>
                                        <p class="text-slate-400">> compiling components...</p>
                                        <p class="text-success">> deployment success! SLA uptime 99.99%</p>
                                        <p class="text-slate-500">> cloud architecture: active</p>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-slate-500 border-t border-slate-800 pt-4 mt-4">
                                        <span>Jakarta, Indonesia</span>
                                        <span>v2.4-stable</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Slide Dots -->
            @if (count($sliders) > 1)
                <div class="flex items-center gap-2 mt-12">
                    @foreach ($sliders as $index => $s)
                        <button 
                            @click="activeSlide = {{ $index }}" 
                            :class="activeSlide === {{ $index }} ? 'w-8 bg-accent' : 'w-2.5 bg-slate-700'"
                            class="h-2.5 rounded-full transition-all duration-300"
                        ></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- 3. Strip Logo Partner (BAB 5, Section 3) -->
    @if ($partners->isNotEmpty())
        <section class="py-10 bg-slate-50 border-y border-slate-200 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-widest mb-6">Dipercaya oleh industri siber & teknologi</p>
                
                <div class="relative w-full overflow-hidden">
                    <style>
                        @keyframes marquee {
                            0% { transform: translateX(0%); }
                            100% { transform: translateX(-50%); }
                        }
                        .animate-marquee-logos {
                            display: flex;
                            width: max-content;
                            animation: marquee 25s linear infinite;
                        }
                    </style>
                    <div class="animate-marquee-logos gap-16 items-center">
                        @foreach ($partners->concat($partners) as $partner)
                            <a 
                                href="{{ $partner->website_url ?: '#' }}" 
                                target="_blank"
                                class="w-32 h-10 flex items-center justify-center opacity-85 hover:opacity-100 transition-all duration-300 flex-shrink-0"
                            >
                                @if ($partner->getFirstMediaUrl('logo'))
                                    <img src="{{ $partner->getFirstMediaUrl('logo') }}" alt="{{ $partner->name }}" class="max-w-full max-h-full object-contain" />
                                @else
                                    <span class="font-bold text-slate-500 text-sm tracking-wide">{{ $partner->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 4. Tentang Kami / Why Us (BAB 5, Section 5) -->
    @if (isset($sections['about_us']) || isset($sections['why_us']))
        <section id="about" class="py-20 md:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-6 space-y-24">
                
                <!-- Section About Us -->
                @if (isset($sections['about_us']))
                    @php $about = $sections['about_us']; @endphp
                    <div 
                        x-data="{ shown: false }" 
                        x-intersect.margin.-15%="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center transition-all duration-700 ease-out"
                    >
                        <!-- Text -->
                        <div class="space-y-6">
                            <span class="text-xs font-bold text-accent uppercase tracking-widest">{{ $about->subtitle }}</span>
                            <h2 class="text-3xl md:text-4xl font-extrabold text-primary leading-tight font-sans">{{ $about->title }}</h2>
                            <p class="text-sm md:text-base text-slate-500 leading-relaxed whitespace-pre-line">
                                {{ $about->content }}
                            </p>
                        </div>
                        
                        <!-- Image / Graphic -->
                        <div class="relative flex justify-center">
                            @if ($about->getFirstMediaUrl('image'))
                                <div class="w-full max-w-md aspect-[4/3] rounded-xl overflow-hidden shadow-lg border border-slate-200">
                                    <img src="{{ $about->getFirstMediaUrl('image') }}" class="w-full h-full object-cover" />
                                </div>
                            @else
                                <!-- Statistics / Experience Trust Box -->
                                <div class="w-full max-w-md bg-primary text-white rounded-xl p-8 border border-slate-800 shadow-2xl grid grid-cols-2 gap-8 relative overflow-hidden">
                                    <!-- Background shape -->
                                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-accent rounded-full opacity-10"></div>
                                    
                                    <div class="col-span-2 pb-4 border-b border-slate-800">
                                        <h3 class="text-sm font-bold text-accent uppercase tracking-widest">Techira Trust Stats</h3>
                                        <p class="text-xs text-slate-400 mt-1">Metrik keberhasilan operasional teknologi.</p>
                                    </div>
                                    
                                    <!-- Stat 1 -->
                                    <div x-data="{ count: 0, target: 6, start() { let interval = setInterval(() => { if (this.count < this.target) { this.count++; } else { clearInterval(interval); } }, 250) } }" x-intersect.once="start()">
                                        <h4 class="text-4xl font-black text-white"><span x-text="count">0</span>+</h4>
                                        <p class="text-[10px] text-slate-400 mt-1 font-semibold uppercase tracking-wider">Tahun Pengalaman</p>
                                    </div>

                                    <!-- Stat 2 -->
                                    <div x-data="{ count: 0, target: 80, start() { let interval = setInterval(() => { if (this.count < this.target) { this.count += 2; } else { this.count = this.target; clearInterval(interval); } }, 25) } }" x-intersect.once="start()">
                                        <h4 class="text-4xl font-black text-white"><span x-text="count">0</span>+</h4>
                                        <p class="text-[10px] text-slate-400 mt-1 font-semibold uppercase tracking-wider">Klien Korporat</p>
                                    </div>

                                    <!-- Stat 3 -->
                                    <div x-data="{ count: 0, target: 120, start() { let interval = setInterval(() => { if (this.count < this.target) { this.count += 4; } else { this.count = this.target; clearInterval(interval); } }, 25) } }" x-intersect.once="start()">
                                        <h4 class="text-4xl font-black text-white"><span x-text="count">0</span>+</h4>
                                        <p class="text-[10px] text-slate-400 mt-1 font-semibold uppercase tracking-wider">Proyek Sukses</p>
                                    </div>

                                    <!-- Stat 4 -->
                                    <div x-data="{ count: 0, target: 99, start() { let interval = setInterval(() => { if (this.count < this.target) { this.count++; } else { clearInterval(interval); } }, 20) } }" x-intersect.once="start()">
                                        <h4 class="text-4xl font-black text-white"><span x-text="count">0</span>%</h4>
                                        <p class="text-[10px] text-slate-400 mt-1 font-semibold uppercase tracking-wider">Customer SLA</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Section Why Us -->
                @if (isset($sections['why_us']))
                    @php $why = $sections['why_us']; @endphp
                    <div 
                        x-data="{ shown: false }" 
                        x-intersect.margin.-15%="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center transition-all duration-700 ease-out"
                    >
                        <!-- Image Area / Fallback Stats -->
                        <div class="relative flex justify-center order-last lg:order-first">
                            @if ($why->getFirstMediaUrl('image'))
                                <div class="w-full max-w-md aspect-[4/3] rounded-xl overflow-hidden shadow-lg border border-slate-200">
                                    <img src="{{ $why->getFirstMediaUrl('image') }}" class="w-full h-full object-cover" />
                                </div>
                            @else
                                <div class="w-full max-w-md flex flex-col gap-6">
                                    <!-- Card 1 -->
                                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm shadow-slate-100/50 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 hover:-translate-y-1">
                                        <div class="flex items-center gap-4">
                                            <div class="p-3 bg-accent/10 rounded-xl text-accent">
                                                <x-lucide-shield class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-primary text-sm tracking-wide">Security Compliance</h4>
                                                <p class="text-xs text-accent mt-0.5 font-semibold">Standard ISO 27001</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-relaxed mt-3">Seluruh proses pengembangan software dan infrastruktur cloud kami diaudit dengan standard ISO 27001 secara konsisten demi keamanan data Anda.</p>
                                    </div>

                                    <!-- Card 2 -->
                                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm shadow-slate-100/50 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 hover:-translate-y-1">
                                        <div class="flex items-center gap-4">
                                            <div class="p-3 bg-accent/10 rounded-xl text-accent">
                                                <x-lucide-zap class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-primary text-sm tracking-wide">Agile Methodology</h4>
                                                <p class="text-xs text-accent mt-0.5 font-semibold">Sprint Delivery 2 Mingguan</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-relaxed mt-3">Pengiriman modul bertahap setiap 2 minggu agar Anda memiliki visibilitas penuh dan kontrol terhadap perkembangan proyek Anda.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Text -->
                        <div class="space-y-6">
                            <span class="text-xs font-bold text-accent uppercase tracking-widest">{{ $why->subtitle }}</span>
                            <h2 class="text-3xl md:text-4xl font-extrabold text-primary leading-tight font-sans">{{ $why->title }}</h2>
                            
                            @php
                                $paragraphs = explode("\n", $why->content);
                                $intro = $paragraphs[0] ?? '';
                                $listItems = [];
                                foreach ($paragraphs as $p) {
                                    if (preg_match('/^\d+\.\s*(.*)/', trim($p), $matches)) {
                                        $listItems[] = $matches[1];
                                    }
                                }
                            @endphp
                            
                            <p class="text-sm md:text-base text-slate-500 leading-relaxed">
                                {{ $intro }}
                            </p>

                            @if (!empty($listItems))
                                <ul class="space-y-4 pt-2">
                                    @foreach ($listItems as $item)
                                        <li class="flex items-start gap-3 text-slate-600 text-sm font-medium">
                                            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-accent/10 flex items-center justify-center text-accent mt-0.5">
                                                <x-lucide-check class="w-3.5 h-3.5" />
                                            </div>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm md:text-base text-slate-500 leading-relaxed whitespace-pre-line">
                                    {{ $why->content }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </section>
    @endif

    <!-- 5. Layanan Jasa Unggulan (BAB 5, Section 4) -->
    @if ($featuredServices->isNotEmpty())
        <section id="services" class="py-20 md:py-28 bg-slate-50 border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header Section -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Layanan Jasa</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Solusi Teknologi Unggulan Kami</h2>
                    <p class="text-sm text-slate-500">Kami menghadirkan keahlian pengembangan aplikasi, cloud infrastructure, dan support siber enterprise.</p>
                </div>

                <!-- Grid Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($featuredServices as $service)
                        <div 
                            x-data="{ shown: false }" 
                            x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col justify-between transition-all duration-500 hover:-translate-y-1 hover:border-accent hover:shadow-lg group"
                        >
                            <div class="space-y-5">
                                <!-- Icon / Image header -->
                                <div class="flex items-center justify-between">
                                    <div class="p-3.5 bg-slate-100 rounded-lg text-primary group-hover:bg-accent/10 group-hover:text-accent transition-colors w-fit">
                                        <x-dynamic-component :component="'lucide-' . ($service->icon ?: 'check')" class="w-6 h-6 transition-transform group-hover:scale-105 duration-300" />
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">
                                        {{ $service->category?->name }}
                                    </span>
                                </div>

                                <!-- Text content -->
                                <div class="space-y-2">
                                    <h3 class="font-bold text-primary text-base group-hover:text-accent transition-colors">{{ $service->name }}</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">{{ $service->short_description }}</p>
                                </div>

                                <!-- Features bullets -->
                                @if ($service->features->isNotEmpty())
                                    <ul class="space-y-2 border-t border-slate-100 pt-4 text-xs text-slate-600">
                                        @foreach ($service->features->take(4) as $feat)
                                            <li class="flex items-center gap-2">
                                                <x-dynamic-component :component="'lucide-' . ($feat->icon ?: 'check')" class="w-4 h-4 text-accent flex-shrink-0" />
                                                <span class="truncate">{{ $feat->title }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <!-- WhatsApp button -->
                            <div class="mt-8 pt-4 border-t border-slate-100">
                                <button 
                                    wire:click="trackServiceWa({{ $service->id }})"
                                    class="w-full flex items-center justify-center gap-2 py-2 border border-slate-200 hover:border-accent hover:bg-accent hover:text-white rounded-lg text-xs font-semibold text-slate-700 transition-all focus:outline-none"
                                >
                                    <x-lucide-phone-call class="w-4 h-4" />
                                    <span>Tanya via WhatsApp</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 6. Produk Unggulan (BAB 5, Section 4) -->
    @if ($featuredProducts->isNotEmpty())
        <section id="products" class="py-20 md:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Produk SaaS & Hardware</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Produk Software Siap Pakai</h2>
                    <p class="text-sm text-slate-500">Optimalkan efisiensi bisnis harian dengan platform software-as-a-service (SaaS) kami.</p>
                </div>

                <!-- Grid Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($featuredProducts as $product)
                        <div 
                            x-data="{ shown: false }" 
                            x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col justify-between transition-all duration-500 hover:-translate-y-1 hover:border-accent hover:shadow-lg group"
                        >
                            <!-- Cover Image -->
                            <div class="w-full aspect-[16/10] bg-slate-100 overflow-hidden border-b border-slate-200 relative">
                                @if ($product->getFirstMediaUrl('image'))
                                    <img src="{{ $product->getFirstMediaUrl('image') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <x-lucide-shopping-bag class="w-12 h-12" />
                                    </div>
                                @endif
                                
                                <span class="absolute top-3 left-3 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary text-white uppercase tracking-wider z-10 shadow-sm">
                                    {{ $product->category?->name }}
                                </span>
                            </div>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <!-- Title & price -->
                                    <div class="space-y-1">
                                        <h3 class="font-bold text-primary text-base group-hover:text-accent transition-colors truncate">{{ $product->name }}</h3>
                                        @if ($product->price)
                                            <p class="text-sm font-extrabold text-accent">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-xs font-semibold text-slate-400">Harga Hubungi Kontak</p>
                                        @endif
                                    </div>

                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">{{ $product->short_description }}</p>
                                </div>

                                <!-- WA action button -->
                                <div class="mt-8 pt-4 border-t border-slate-100">
                                    <button 
                                        wire:click="trackProductWa({{ $product->id }})"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 bg-accent hover:bg-accent/90 text-white font-semibold rounded-lg text-xs transition-colors shadow-sm focus:outline-none"
                                    >
                                        <x-lucide-phone-call class="w-4 h-4" />
                                        <span>Tanya via WhatsApp</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 7. Tim Kami (BAB 5, Section 6) -->
    @if ($team->isNotEmpty())
        <section id="team" class="py-20 md:py-28 bg-slate-50 border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Struktur Tim</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Para Ahli & Pengembang Profesional</h2>
                    <p class="text-sm text-slate-500">Mengenal lebih dekat tim software engineer dan cloud architect di balik keandalan platform kami.</p>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($team as $member)
                        <div 
                            x-data="{ shown: false }" 
                            x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col items-center text-center space-y-4 group transition-all duration-500 hover:shadow-md"
                        >
                            <!-- Image Grayscale to Color -->
                            <div class="w-28 h-28 rounded-full overflow-hidden bg-slate-200 border border-slate-300 shadow-inner flex-shrink-0 flex items-center justify-center font-bold text-slate-500 uppercase text-2xl relative">
                                @if ($member->getFirstMediaUrl('photo'))
                                    <img src="{{ $member->getFirstMediaUrl('photo') }}" alt="{{ $member->name }}" class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-500" />
                                @else
                                    {{ substr($member->name, 0, 2) }}
                                @endif
                            </div>

                            <div>
                                <h4 class="text-base font-bold text-primary">{{ $member->name }}</h4>
                                <p class="text-xs text-accent mt-1 font-semibold">{{ $member->position }}</p>
                            </div>

                            @if ($member->bio)
                                <p class="text-xs text-slate-500 leading-relaxed max-w-xs line-clamp-3">
                                    {{ $member->bio }}
                                </p>
                            @endif

                            <!-- Social links -->
                            @if (!empty($member->social_links))
                                <div class="flex items-center gap-3 pt-2 text-slate-400">
                                    @if (isset($member->social_links['facebook']))
                                        <a href="{{ $member->social_links['facebook'] }}" target="_blank" class="hover:text-accent transition-colors"><x-lucide-facebook class="w-4 h-4" /></a>
                                    @endif
                                    @if (isset($member->social_links['instagram']))
                                        <a href="{{ $member->social_links['instagram'] }}" target="_blank" class="hover:text-accent transition-colors"><x-lucide-instagram class="w-4 h-4" /></a>
                                    @endif
                                    @if (isset($member->social_links['linkedin']))
                                        <a href="{{ $member->social_links['linkedin'] }}" target="_blank" class="hover:text-accent transition-colors"><x-lucide-linkedin class="w-4 h-4" /></a>
                                    @endif
                                    @if (isset($member->social_links['github']))
                                        <a href="{{ $member->social_links['github'] }}" target="_blank" class="hover:text-accent transition-colors"><x-lucide-github class="w-4 h-4" /></a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 8. Testimoni Klien (BAB 5, Section 7) -->
    @if ($testimonials->isNotEmpty())
        <section class="py-20 md:py-28 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Header -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Testimoni</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Kata Mereka Tentang Kami</h2>
                    <p class="text-sm text-slate-500">Membaca ulasan kepuasan dan review langsung dari mitra kerja sama kami.</p>
                </div>

                <!-- Grid (Simple Flat Grid Layout, anti slop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($testimonials as $testimonial)
                        <div 
                            x-data="{ shown: false }" 
                            x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-slate-50 rounded-lg border border-slate-200 p-6 flex flex-col justify-between space-y-6 transition-all duration-500"
                        >
                            <p class="text-xs text-slate-600 italic leading-relaxed">
                                "{{ $testimonial->message }}"
                            </p>

                            <div class="flex items-center gap-4 border-t border-slate-200 pt-4">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 uppercase text-xs">
                                    @if ($testimonial->getFirstMediaUrl('photo'))
                                        <img src="{{ $testimonial->getFirstMediaUrl('photo') }}" alt="{{ $testimonial->name }}" class="w-full h-full object-cover" />
                                    @else
                                        {{ substr($testimonial->name, 0, 2) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-primary truncate">{{ $testimonial->name }}</h4>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $testimonial->position }} at {{ $testimonial->company }}</p>
                                    
                                    <div class="flex items-center gap-0.5 mt-1.5 text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-lucide-star class="w-3 h-3 {{ $i <= $testimonial->rating ? 'fill-current' : 'text-slate-300' }}" />
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 9. FAQ Accordion (BAB 5, Section 8) -->
    @if ($faqs->isNotEmpty())
        <section id="faq" class="py-20 md:py-28 bg-slate-50 border-t border-slate-200">
            <div class="max-w-3xl mx-auto px-6">
                <!-- Header -->
                <div class="text-center mb-16 space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Pertanyaan Umum</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Frequently Asked Questions</h2>
                    <p class="text-sm text-slate-500">Jawaban atas pertanyaan-pertanyaan yang sering ditanyakan oleh klien baru.</p>
                </div>

                <!-- Accordion (Alpine) -->
                <div class="space-y-4" x-data="{ activeIndex: null }">
                    @foreach ($faqs as $index => $faq)
                        <div 
                            class="bg-white border border-slate-100 rounded-2xl shadow-sm shadow-slate-100/30 overflow-hidden transition-all duration-300 hover:shadow-md hover:shadow-slate-200/40 hover:border-slate-200"
                            :class="activeIndex === {{ $index }} ? 'ring-1 ring-accent/30 border-accent/20' : ''"
                        >
                            <button 
                                @click="activeIndex = (activeIndex === {{ $index }} ? null : {{ $index }})"
                                class="w-full flex items-center justify-between px-6 py-5 text-left font-bold text-sm md:text-base text-primary hover:text-accent transition-colors focus:outline-none group"
                            >
                                <span class="pr-4">{{ $faq->question }}</span>
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-accent group-hover:bg-accent/5 transition-all duration-300" :class="activeIndex === {{ $index }} ? 'bg-accent/10 text-accent' : ''">
                                    <x-lucide-chevron-down 
                                        class="w-4 h-4 transition-transform duration-300"
                                        ::class="activeIndex === {{ $index }} ? 'rotate-180' : ''"
                                    />
                                </div>
                            </button>
                            
                            <div 
                                x-show="activeIndex === {{ $index }}"
                                x-collapse
                                class="px-6 pb-6 text-xs md:text-sm text-slate-500 leading-relaxed border-t border-slate-50 pt-4 bg-slate-50/20"
                                style="display: none;"
                            >
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 10. CTA Kontak / Hubungi Kami (BAB 5, Section 9) -->
    <section id="contact" class="py-20 md:py-28 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white relative overflow-hidden border-t border-slate-900">
        <!-- Background Grid Effect -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-35"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-16 items-start">
            <!-- Left Info column (2 cols) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Hubungi Kami</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight">Mulai Konsultasi IT Gratis Sekarang</h2>
                    <p class="text-sm text-slate-400 leading-relaxed">Punya rencana proyek digital atau butuh bantuan siber? Tuliskan kendala Anda dan tim developer kami siap mendampingi.</p>
                </div>

                <div class="space-y-4 text-sm text-slate-300">
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-map-pin class="w-5 h-5" />
                        </div>
                        <span class="leading-relaxed">{{ $settings['address'] }}</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-phone class="w-5 h-5" />
                        </div>
                        <span>{{ $settings['phone'] }}</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-mail class="w-5 h-5" />
                        </div>
                        <span>{{ $settings['email'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Form column (3 cols) -->
            <div class="lg:col-span-3 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 p-6 md:p-8 rounded-2xl shadow-2xl space-y-6">
                <h3 class="font-bold text-white text-base">Kirim Formulir Kontak</h3>
                
                <form wire:submit.prevent="submitContact" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <input 
                                type="text" 
                                wire:model="name"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Nama Lengkap *"
                            />
                            @error('name') <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Email Kantor *"
                            />
                            @error('email') <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <input 
                                type="text" 
                                wire:model="phone"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Nomor Telepon (Opsional)"
                            />
                            @error('phone') <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <input 
                                type="text" 
                                wire:model="subject"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Subjek Pesan"
                            />
                            @error('subject') <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <textarea 
                            wire:model="message"
                            rows="4"
                            class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                            placeholder="Tuliskan kendala IT atau spesifikasi project yang ingin dibuat... *"
                        ></textarea>
                        @error('message') <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit -->
                    <div>
                        <button 
                            type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-accent to-indigo-600 hover:from-accent/90 hover:to-indigo-600/90 text-white font-bold rounded-xl text-xs transition-all duration-300 shadow-md shadow-accent/10 hover:shadow-accent/25 hover:scale-[1.01] focus:outline-none"
                        >
                            <span wire:loading.remove wire:target="submitContact">Kirim Pesan</span>
                            <span wire:loading wire:target="submitContact" class="flex items-center justify-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                <span>Mengirim...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 11. Footer (BAB 5, Section 10) -->
    <footer class="bg-slate-950 text-slate-400 text-xs py-16 border-t border-slate-900 relative">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <!-- Company Info -->
            <div class="space-y-4">
                <a href="#" class="flex items-center gap-2 font-extrabold text-white text-lg tracking-wider transition-transform duration-300 hover:scale-[1.02]">
                    @if ($settings['logo'])
                        <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] }}" class="h-9 max-w-[150px] object-contain" />
                    @else
                        <x-lucide-terminal class="w-5 h-5 text-accent" />
                        <span>TECHIRA</span>
                    @endif
                </a>
                <p class="leading-relaxed mt-2 text-slate-500">Solusi teknologi software, cloud architecture, devops, dan jaringan enterprise terpercaya di Indonesia.</p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="font-bold text-white text-sm">Tautan Cepat</h4>
                <ul class="space-y-3 text-slate-500 font-medium">
                    <li><a href="#" class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Home</span></a></li>
                    <li><a href="#about" class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Tentang Kami</span></a></li>
                    <li><a href="#services" class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Layanan Jasa</span></a></li>
                    <li><a href="#products" class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Produk SaaS</span></a></li>
                </ul>
            </div>

            <!-- Contacts -->
            <div class="space-y-4">
                <h4 class="font-bold text-white text-sm">Kontak Kami</h4>
                <ul class="space-y-2 text-slate-500">
                    <li>{{ $settings['address'] }}</li>
                    <li>Email: {{ $settings['email'] }}</li>
                    <li>Telp: {{ $settings['phone'] }}</li>
                </ul>
            </div>

            <!-- Social Media URLs -->
            <div class="space-y-4">
                <h4 class="font-bold text-white text-sm">Ikuti Kami</h4>
                <div class="flex items-center gap-3">
                    <a href="{{ $settings['social_facebook'] }}" target="_blank" class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-facebook class="w-4 h-4" /></a>
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-instagram class="w-4 h-4" /></a>
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-linkedin class="w-4 h-4" /></a>
                    <a href="{{ $settings['youtube_url'] }}" target="_blank" class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-youtube class="w-4 h-4" /></a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 border-t border-slate-900 pt-6 text-center text-slate-600">
            <p>&copy; {{ date('Y') }} {{ $settings['company_name'] }}. Hak cipta dilindungi undang-undang.</p>
        </div>
    </footer>

    <!-- Floating WhatsApp Widget (CTWA) -->
    @if ($settings['whatsapp_number'])
        @php
            $cleanWa = preg_replace('/[^0-9]/', '', $settings['whatsapp_number']);
            // If number starts with 0, convert to 62
            if (str_starts_with($cleanWa, '0')) {
                $cleanWa = '62' . substr($cleanWa, 1);
            }
            $waUrl = 'https://wa.me/' . $cleanWa . '?text=' . urlencode(str_replace(['{name}', '{url}'], ['Layanan Konsultasi', url('/')], $settings['whatsapp_message_template']));
        @endphp
        <div x-data="{ showConfirm: false }">
            <!-- Button -->
            <button 
                @click="showConfirm = true"
                type="button"
                class="fixed bottom-6 right-6 z-50 rounded-full shadow-2xl transition-all duration-300 hover:scale-110 focus:outline-none text-white"
                style="width: 56px; height: 56px; background-color: #25D366; display: flex; align-items: center; justify-content: center;"
                title="Konsultasi WhatsApp"
            >
                <svg class="fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 28px; height: 28px;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                </svg>
                <span style="position: absolute; top: -1px; right: -1px; display: flex; width: 14px; height: 14px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-500"></span>
                </span>
            </button>

            <!-- Modal Backdrop -->
            <div 
                x-show="showConfirm" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                style="display: none;"
                @click="showConfirm = false"
            >
                <!-- Modal Card -->
                <div 
                    x-show="showConfirm"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xs p-5 shadow-2xl relative space-y-5"
                    @click.stop
                >
                    <!-- Close button -->
                    <button @click="showConfirm = false" class="absolute top-3.5 right-3.5 text-slate-400 hover:text-white transition-colors focus:outline-none">
                        <x-lucide-x class="w-4 h-4" />
                    </button>

                    <!-- Icon & Title -->
                    <div class="flex flex-col items-center text-center space-y-3">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 56px; height: 56px; fill: #25D366;">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                        <h3 class="font-extrabold text-white text-base">Mulai Konsultasi?</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed max-w-[200px]">
                            Hubungi tim ahli kami sekarang via WhatsApp untuk konsultasi gratis.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 pt-2">
                        <a 
                            href="{{ $waUrl }}"
                            target="_blank"
                            @click="showConfirm = false"
                            class="w-full flex items-center justify-center py-2.5 bg-[#25D366] hover:bg-[#20BA56] text-white font-extrabold text-[11px] uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-[#25D366]/10"
                        >
                            <span>Hubungi WhatsApp</span>
                        </a>
                        <button 
                            @click="showConfirm = false"
                            type="button"
                            class="w-full py-2.5 bg-slate-800 hover:bg-slate-750 text-slate-400 font-extrabold text-[11px] uppercase tracking-wider rounded-xl transition-colors border border-slate-700/40"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>