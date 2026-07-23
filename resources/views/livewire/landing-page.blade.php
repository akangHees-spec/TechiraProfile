@php
    $navItems = \App\Models\NavItem::whereNull('parent_id')
        ->where('is_active', true)
        ->with(['children' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }])
        ->orderBy('order')
        ->get();
@endphp
<div x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)"
    class="min-h-screen bg-white text-slate-800 selection:bg-accent selection:text-white" x-init="window.addEventListener('redirect-to', e => {
        window.open(e.detail.url, '_blank');
    });">
    <!-- Toast Notification (Contact Success) -->
    @if (session()->has('contact_toast'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 w-full max-w-sm p-4 bg-white border border-slate-200 rounded-lg shadow-xl">
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
    <header
        :class="scrolled ? 'bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/40 shadow-lg shadow-black/10 py-3.5' :
            'bg-transparent py-6'"
        class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <!-- Logo -->
            <a href="#"
                class="flex items-center gap-2 font-extrabold text-xl tracking-wider text-white transition-transform duration-300 hover:scale-[1.02]">
                @if ($settings['logo'])
                    <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] }}"
                        class="h-10 max-w-[170px] object-contain" />
                @else
                    <x-lucide-terminal class="w-6 h-6 text-accent" />
                @endif
                <span>{{ $settings['company_name'] }}</span>
            </a>

            <!-- Menu Desktop -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-400">
                @foreach ($navItems as $item)
                    @if ($item->children->count() > 0)
                        <!-- Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-1.5 py-1.5 hover:text-white transition-colors focus:outline-none">
                                <span>{{ $item->label }}</span>
                                <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform duration-200" ::class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" x-transition class="absolute top-full left-0 mt-2 w-48 bg-slate-900 border border-slate-800/80 rounded-xl p-2 shadow-2xl space-y-1 z-55" style="display: none;">
                                @foreach ($item->children as $child)
                                    <a href="{{ $child->url ?: '/' }}" class="block px-3 py-2 text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Simple Link -->
                        <a href="{{ $item->url ?: '#' }}" class="relative py-1.5 hover:text-white transition-colors group">
                            <span>{{ $item->label }}</span>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- CTA Button -->
            <div class="hidden md:flex items-center gap-4">
                <a href="#contact"
                    class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white font-bold text-xs rounded-lg transition-all duration-300 shadow-md shadow-accent/10 hover:shadow-accent/20 hover:scale-[1.03]">
                    Hubungi Kami
                </a>
            </div>

            <!-- Hamburger Button (Mobile) -->
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-white hover:text-accent focus:outline-none">
                    <x-lucide-menu x-show="!open" class="w-6 h-6" />
                    <x-lucide-x x-show="open" class="w-6 h-6" style="display: none;" />
                </button>

                <!-- Mobile Menu Dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute top-full left-0 right-0 mt-2 bg-slate-900 border border-slate-800 rounded-lg p-5 shadow-2xl space-y-4"
                    style="display: none;">
                    @foreach ($navItems as $item)
                        @if ($item->children->count() > 0)
                            <div x-data="{ openSub: false }">
                                <button @click="openSub = !openSub" class="w-full flex items-center justify-between text-slate-300 hover:text-white text-sm font-semibold py-2 text-left focus:outline-none">
                                    <span>{{ $item->label }}</span>
                                    <x-lucide-chevron-down class="w-4 h-4 transition-transform" ::class="openSub ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="openSub" x-transition class="pl-4 space-y-2 mt-1" style="display: none;">
                                    @foreach ($item->children as $child)
                                        <a @click="$parent.open = false" href="{{ $child->url ?: '#' }}" class="block text-slate-400 hover:text-white text-xs font-semibold py-1.5">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a @click="open = false" href="{{ $item->url ?: '#' }}" class="block text-slate-300 hover:text-white text-sm font-semibold py-2">
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                    <a @click="open = false" href="#contact"
                        class="block w-full text-center px-4 py-2.5 bg-accent text-white text-xs font-bold rounded-lg hover:bg-accent/90 transition-colors">Hubungi
                        Kami</a>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Hero Section (with Slider Carousel) -->
    <section class="relative bg-primary text-white pt-32 pb-24 md:pt-48 md:pb-36 overflow-hidden">

        <!-- Subtle Grid Overlay -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-35">
        </div>

        <!-- Slider Carousel (Alpine.js) -->
        <div x-data="{ activeSlide: 0, maxSlide: {{ count($sliders) - 1 }} }" class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="relative min-h-[300px] md:min-h-[380px] flex items-center">
                @foreach ($sliders as $index => $slider)
                    <div x-show="activeSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-500 transform"
                        x-transition:enter-start="opacity-0 translate-x-12"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300 transform absolute"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-12"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full"
                        style="{{ $index > 0 ? 'display: none;' : '' }}">
                        <!-- Copywriting Content -->
                        <div class="space-y-6">
                            <h1
                                class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight font-sans">
                                {{ $slider->title }}
                            </h1>
                            <p class="text-base md:text-lg text-slate-300 leading-relaxed max-w-xl">
                                {{ $slider->subtitle }}
                            </p>
                            @if ($slider->button_text)
                                <div class="pt-4 flex flex-wrap gap-4">
                                    <a href="{{ $slider->button_link }}"
                                        class="px-6 py-3 bg-accent hover:bg-accent/90 text-white font-semibold rounded-lg shadow-sm transition-colors text-sm">
                                        {{ $slider->button_text }}
                                    </a>
                                    <a href="#services"
                                        class="px-6 py-3 border border-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors text-sm">
                                        Lihat Layanan
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Hero Flat Visual Mockup -->
                        <div class="relative flex justify-center">
                            @if ($slider->getFirstMediaUrl('image'))
                                <div
                                    class="w-full max-w-lg aspect-[16/10] rounded-xl overflow-hidden shadow-2xl border border-slate-800 bg-slate-900/50 p-2">
                                    <img src="{{ $slider->getFirstMediaUrl('image') }}"
                                        class="w-full h-full object-cover rounded-lg" />
                                </div>
                            @else
                                <!-- High-quality Flat Graphic Box (Bukan slop AI) -->
                                <div
                                    class="w-full max-w-lg aspect-[16/10] bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-2xl flex flex-col justify-between">
                                    <div class="flex items-center gap-2 text-slate-500 font-mono text-xs">
                                        <span class="w-3 h-3 bg-danger rounded-full"></span>
                                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                        <span class="w-3 h-3 bg-success rounded-full"></span>
                                        <span class="ml-2">techira-console-v1.log</span>
                                    </div>
                                    <div
                                        class="flex-1 flex flex-col justify-center font-mono text-sm text-accent space-y-2 mt-4">
                                        <p class="text-white">$ npm run build --prod</p>
                                        <p class="text-slate-400">> compiling components...</p>
                                        <p class="text-success">> deployment success! SLA uptime 99.99%</p>
                                        <p class="text-slate-500">> cloud architecture: active</p>
                                    </div>
                                    <div
                                        class="flex justify-between items-center text-xs text-slate-500 border-t border-slate-800 pt-4 mt-4">
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
                        <button @click="activeSlide = {{ $index }}"
                            :class="activeSlide === {{ $index }} ? 'w-8 bg-accent' : 'w-2.5 bg-slate-700'"
                            class="h-2.5 rounded-full transition-all duration-300"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- 3. Strip Logo Partner (BAB 5, Section 3) -->
    @if ($partners->isNotEmpty())
        <section class="py-10 bg-slate-50 border-y border-slate-200 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-widest mb-6">Dipercaya
                    oleh industri siber & teknologi</p>

                <div class="relative w-full overflow-hidden">
                    <style>
                        @keyframes marquee {
                            0% {
                                transform: translateX(0%);
                            }

                            100% {
                                transform: translateX(-50%);
                            }
                        }

                        .animate-marquee-logos {
                            display: flex;
                            width: max-content;
                            animation: marquee 25s linear infinite;
                        }
                    </style>
                    <div class="animate-marquee-logos gap-16 items-center">
                        @foreach ($partners->concat($partners) as $partner)
                            <a href="{{ $partner->website_url ?: '#' }}" target="_blank"
                                class="w-32 h-10 flex items-center justify-center opacity-85 hover:opacity-100 transition-all duration-300 flex-shrink-0">
                                @if ($partner->getFirstMediaUrl('logo'))
                                    <img src="{{ $partner->getFirstMediaUrl('logo') }}" alt="{{ $partner->name }}"
                                        class="max-w-full max-h-full object-contain" />
                                @else
                                    <span
                                        class="font-bold text-slate-500 text-sm tracking-wide">{{ $partner->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 4. Dynamic Sections -->
    @if ($sections->isNotEmpty())
        <section id="about" class="py-20 md:py-28 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 space-y-24 md:space-y-32">
                @foreach($sections as $key => $section)
                    <div x-data="{ shown: false }" x-intersect.margin.-15%="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center transition-all duration-700 ease-out">
                        
                        <!-- Text -->
                        <div class="space-y-6 {{ $loop->iteration % 2 == 0 ? 'order-last lg:order-last' : 'order-last lg:order-first' }}">
                            @if($section->subtitle)
                                <span class="text-xs font-bold text-accent uppercase tracking-widest">{{ $section->subtitle }}</span>
                            @endif
                            <h2 class="text-3xl md:text-4xl font-extrabold text-primary leading-tight font-sans">
                                {{ $section->title }}
                            </h2>
                            
                            @php
                                $paragraphs = explode("\n", $section->content);
                                $listItems = [];
                                $textParagraphs = [];
                                foreach ($paragraphs as $p) {
                                    if (preg_match('/^\d+\.\s*(.*)/', trim($p), $matches) || preg_match('/^-\s*(.*)/', trim($p), $matches)) {
                                        $listItems[] = $matches[1];
                                    } else if (trim($p) !== '') {
                                        $textParagraphs[] = trim($p);
                                    }
                                }
                            @endphp

                            <div class="text-sm md:text-base text-slate-500 leading-relaxed space-y-4">
                                @foreach($textParagraphs as $tp)
                                    <p>{{ $tp }}</p>
                                @endforeach
                            </div>

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
                            @endif
                        </div>

                        <!-- Image / Graphic -->
                        <div class="relative flex justify-center {{ $loop->iteration % 2 == 0 ? 'order-first lg:order-first' : 'order-first lg:order-last' }}">
                            @if ($section->getFirstMediaUrl('image'))
                                <div class="w-full max-w-md md:max-w-lg aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl shadow-slate-200/50 border border-slate-100 group relative">
                                    <div class="absolute inset-0 bg-accent/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none"></div>
                                    <img src="{{ $section->getFirstMediaUrl('image') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                                </div>
                            @else
                                <!-- Dynamic Pattern Box if no image -->
                                <div class="w-full max-w-md md:max-w-lg aspect-[4/3] rounded-2xl bg-slate-900 border border-slate-800 flex flex-col items-center justify-center shadow-2xl relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:2rem_2rem] opacity-20"></div>
                                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-accent rounded-full opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                                    <x-lucide-layers class="w-16 h-16 text-slate-700 mb-4 group-hover:text-accent transition-colors duration-500 relative z-10" />
                                    <span class="text-slate-500 font-mono text-sm tracking-widest relative z-10 group-hover:text-white transition-colors duration-500">TECHIRA.{{ strtoupper($key) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
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
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Solusi
                        Teknologi Unggulan Kami</h2>
                    <p class="text-sm text-slate-500">Kami menghadirkan keahlian pengembangan aplikasi, cloud
                        infrastructure, dan support siber enterprise.</p>
                </div>

                <!-- Grid Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($featuredServices as $service)
                        <div x-data="{ shown: false }" x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col justify-between transition-all duration-500 hover:-translate-y-1 hover:border-accent hover:shadow-lg group">
                            <div class="space-y-5">
                                <!-- Icon / Image header -->
                                <div class="flex items-center justify-between">
                                    <div
                                        class="p-3.5 bg-slate-100 rounded-lg text-primary group-hover:bg-accent/10 group-hover:text-accent transition-colors w-fit">
                                        <x-dynamic-component :component="'lucide-' . ($service->icon ?: 'check')"
                                            class="w-6 h-6 transition-transform group-hover:scale-105 duration-300" />
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">
                                        {{ $service->category?->name }}
                                    </span>
                                </div>

                                <!-- Text content -->
                                <div class="space-y-2">
                                    <h3
                                        class="font-bold text-primary text-base group-hover:text-accent transition-colors">
                                        {{ $service->name }}</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        {{ $service->short_description }}</p>
                                </div>

                                <!-- Features bullets -->
                                @if ($service->features->isNotEmpty())
                                    <ul class="space-y-2 border-t border-slate-100 pt-4 text-xs text-slate-600">
                                        @foreach ($service->features->take(4) as $feat)
                                            <li class="flex items-center gap-2">
                                                <x-dynamic-component :component="'lucide-' . ($feat->icon ?: 'check')"
                                                    class="w-4 h-4 text-accent flex-shrink-0" />
                                                <span class="truncate">{{ $feat->title }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <!-- Action button -->
                            <div class="mt-8 pt-4 border-t border-slate-100">
                                <a href="{{ route('services.show', $service->slug) }}"
                                    class="w-full flex items-center justify-center gap-2 py-2 border border-slate-200 hover:border-accent hover:bg-accent hover:text-white rounded-lg text-xs font-semibold text-slate-700 transition-all focus:outline-none">
                                    <x-lucide-arrow-right class="w-4 h-4" />
                                    <span>Lihat Detail</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 hover:border-accent text-slate-700 hover:text-accent font-semibold rounded-lg shadow-sm transition-all text-sm group">
                        <span>Lihat Semua Layanan</span>
                        <x-lucide-arrow-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </a>
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
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Produk
                        Software Siap Pakai</h2>
                    <p class="text-sm text-slate-500">Optimalkan efisiensi bisnis harian dengan platform
                        software-as-a-service (SaaS) kami.</p>
                </div>

                <!-- Grid Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($featuredProducts as $product)
                        <div x-data="{ shown: false }" x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col justify-between transition-all duration-500 hover:-translate-y-1 hover:border-accent hover:shadow-lg group">
                            <!-- Cover Image -->
                            <div
                                class="w-full aspect-[16/10] bg-slate-100 overflow-hidden border-b border-slate-200 relative">
                                @if ($product->getFirstMediaUrl('image'))
                                    <img src="{{ $product->getFirstMediaUrl('image') }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <x-lucide-shopping-bag class="w-12 h-12" />
                                    </div>
                                @endif

                                <span
                                    class="absolute top-3 left-3 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary text-white uppercase tracking-wider z-10 shadow-sm">
                                    {{ $product->category?->name }}
                                </span>
                            </div>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <!-- Title & price -->
                                    <div class="space-y-1">
                                        <h3
                                            class="font-bold text-primary text-base group-hover:text-accent transition-colors truncate">
                                            {{ $product->name }}</h3>
                                        @if ($product->price)
                                            <p class="text-sm font-extrabold text-accent">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-xs font-semibold text-slate-400">Harga Hubungi Kontak</p>
                                        @endif
                                    </div>

                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                                        {{ $product->short_description }}</p>
                                </div>

                                <!-- Action button -->
                                <div class="mt-8 pt-4 border-t border-slate-100">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 bg-accent hover:bg-accent/90 text-white font-semibold rounded-lg text-xs transition-colors shadow-sm focus:outline-none">
                                        <x-lucide-eye class="w-4 h-4" />
                                        <span>Lihat Detail</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 hover:border-accent text-slate-700 hover:text-accent font-semibold rounded-lg shadow-sm transition-all text-sm group">
                        <span>Lihat Semua Produk</span>
                        <x-lucide-arrow-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- 7. Tim Kami — SLIDESHOW CAROUSEL -->
    @if ($team->isNotEmpty())
        @php
            $teamMembersData = [];
            foreach ($team as $m) {
                $teamMembersData[] = [
                    'name'     => $m->name,
                    'position' => $m->position,
                    'bio'      => $m->bio ?? '',
                    'photo'    => $m->getFirstMediaUrl('photo'),
                    'initials' => mb_strtoupper(mb_substr($m->name, 0, 2)),
                    'social'   => [
                        'linkedin'  => $m->social_links['linkedin']  ?? null,
                        'github'    => $m->social_links['github']    ?? null,
                        'instagram' => $m->social_links['instagram'] ?? null,
                        'facebook'  => $m->social_links['facebook']  ?? null,
                    ],
                ];
            }
        @endphp

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('teamSlider', () => ({
                    members: @json($teamMembersData),
                    active: 0,
                    visible: true,
                    paused: false,
                    timer: null,
                    get current() { return this.members[this.active] || {}; },
                    get count() { return this.members.length; },
                    goTo(index) {
                        if (index === this.active) return;
                        this.visible = false;
                        setTimeout(() => {
                            this.active = ((index % this.count) + this.count) % this.count;
                            this.visible = true;
                            this.$nextTick(() => {
                                const row = this.$refs.avatarRow;
                                if (row) {
                                    const btn = row.querySelector('[data-idx=\'' + this.active + '\']');
                                    if (btn) btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                                }
                            });
                        }, 200);
                    },
                    prev() { this.goTo(this.active - 1); },
                    next() { this.goTo(this.active + 1); },
                    startAuto() {
                        this.timer = setInterval(() => { if (!this.paused) this.next(); }, 6000);
                    },
                    init() {
                        this.startAuto();
                    }
                }));
            });
        </script>

        <section id="team" class="py-20 md:py-28 bg-[#F8FAFC] border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-6">

                <!-- Header Section -->
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-bold text-[#2563EB] uppercase tracking-widest">Struktur Tim</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#0F172A] tracking-tight mt-3 mb-3">
                        Para Ahli & Pengembang Profesional
                    </h2>
                    <p class="text-sm text-[#64748B] leading-relaxed">
                        Mengenal lebih dekat tim software engineer dan cloud architect di balik keandalan platform kami.
                    </p>
                </div>

                <!-- Alpine Carousel Root -->
                <div
                    x-data="teamSlider"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    class="flex flex-col gap-10"
                >

                    <!-- Featured Member Container -->
                    <div class="relative">
                        <div
                            x-show="visible"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center"
                        >

                            <!-- Kolom Kiri: Informasi Member -->
                            <div class="order-2 md:order-1 flex flex-col gap-5 min-h-[340px] justify-center">

                                <!-- Breadcrumb -->
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest">
                                    Tim <span class="text-[#2563EB] mx-1">/</span>
                                    <span x-text="current.name"></span>
                                </p>

                                <!-- Nama & Jabatan -->
                                <div>
                                    <h3 class="text-3xl md:text-4xl font-extrabold text-[#0F172A] leading-tight"
                                        x-text="current.name"></h3>
                                    <p class="text-base font-bold text-[#2563EB] mt-1.5"
                                        x-text="current.position"></p>
                                </div>

                                <!-- Social Media Links -->
                                <div class="flex items-center gap-3">
                                    <template x-if="current.social && current.social.linkedin">
                                        <a :href="current.social.linkedin" target="_blank" rel="noopener"
                                            class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB] transition-all duration-150">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                        </a>
                                    </template>
                                    <template x-if="current.social && current.social.github">
                                        <a :href="current.social.github" target="_blank" rel="noopener"
                                            class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB] transition-all duration-150">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/></svg>
                                        </a>
                                    </template>
                                    <template x-if="current.social && current.social.instagram">
                                        <a :href="current.social.instagram" target="_blank" rel="noopener"
                                            class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB] transition-all duration-150">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5-5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg>
                                        </a>
                                    </template>
                                    <template x-if="current.social && current.social.facebook">
                                        <a :href="current.social.facebook" target="_blank" rel="noopener"
                                            class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB] transition-all duration-150">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96A10 10 0 0 0 22 12.06C22 6.53 17.5 2.04 12 2.04z"/></svg>
                                        </a>
                                    </template>
                                </div>

                                <!-- Bio -->
                                <p class="text-sm sm:text-base text-[#64748B] leading-relaxed"
                                    x-text="current.bio || 'Bio belum tersedia.'"></p>

                                <!-- Posisi Indikator (01 / 04) -->
                                <div class="flex items-baseline gap-2 pt-3 border-t border-slate-100">
                                    <span class="text-3xl md:text-4xl font-black text-[#2563EB]/20 leading-none select-none tabular-nums"
                                        x-text="String(active + 1).padStart(2, '0')"></span>
                                    <span class="text-xs text-slate-400 font-semibold"
                                        x-text="'/ ' + String(count).padStart(2, '0')"></span>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Foto Potret Utama (Strict Outer Bounds) -->
                            <div class="order-1 md:order-2 relative flex justify-center">
                                <!-- Backdrop Glow Background -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none" aria-hidden="true">
                                    <div class="w-72 h-72 bg-[#2563EB] rounded-full blur-3xl opacity-[0.08]"></div>
                                </div>

                                <!-- Box Pembungkus Utama Foto (Aspek Potret 3:4 dengan rounded-2xl & overflow-hidden ketat) -->
                                <div class="relative w-full max-w-xs md:max-w-sm aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border border-slate-200/60 bg-[#0F1B3D]">
                                    <template x-if="current.photo">
                                        <img
                                            :src="current.photo"
                                            :alt="current.name"
                                            class="w-full h-full object-cover object-top block"
                                        />
                                    </template>
                                    <template x-if="!current.photo">
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#0F1B3D] to-[#1E3A8A]">
                                            <span class="text-6xl font-black text-white/30 uppercase select-none"
                                                x-text="current.initials"></span>
                                        </div>
                                    </template>
                                    <!-- Gradasi Tint Halus di Bawah Foto -->
                                    <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-[#0F1B3D]/30 to-transparent pointer-events-none"></div>
                                </div>

                                <!-- Tombol Navigasi Panah Kiri -->
                                <button
                                    type="button"
                                    @click.prevent="prev()"
                                    class="absolute left-0 md:-left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white border border-slate-200 shadow-xl flex items-center justify-center text-slate-600 hover:text-[#2563EB] hover:border-[#2563EB] hover:scale-110 transition-all duration-150 focus:outline-none"
                                    aria-label="Anggota sebelumnya"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>

                                <!-- Tombol Navigasi Panah Kanan -->
                                <button
                                    type="button"
                                    @click.prevent="next()"
                                    class="absolute right-0 md:-right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white border border-slate-200 shadow-xl flex items-center justify-center text-slate-600 hover:text-[#2563EB] hover:border-[#2563EB] hover:scale-110 transition-all duration-150 focus:outline-none"
                                    aria-label="Anggota berikutnya"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- Baris Selector Avatar (Bulat Kecil di Bawah) -->
                    <div
                        x-ref="avatarRow"
                        class="flex items-start gap-5 overflow-x-auto pb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                    >
                        @foreach ($teamMembersData as $member)
                            @php $idx = $loop->index; @endphp
                            <button
                                type="button"
                                data-idx="{{ $idx }}"
                                @click.prevent="goTo({{ $idx }})"
                                class="flex flex-col items-center gap-2 flex-shrink-0 focus:outline-none rounded-full"
                                :style="active === {{ $idx }} ? 'opacity: 1; filter: none;' : 'opacity: 0.5; filter: grayscale(100%);'"
                                style="transition: opacity 250ms ease, filter 250ms ease;"
                            >
                                <div
                                    class="rounded-full overflow-hidden flex items-center justify-center font-bold text-white uppercase text-sm flex-shrink-0"
                                    :class="active === {{ $idx }}
                                        ? 'w-16 h-16 ring-2 ring-offset-2 ring-[#2563EB] shadow-lg shadow-[#2563EB]/20'
                                        : 'w-14 h-14'"
                                    style="background-color: #0F1B3D; transition: width 200ms ease, height 200ms ease, box-shadow 200ms ease;"
                                >
                                    @if ($member['photo'])
                                        <img
                                            src="{{ $member['photo'] }}"
                                            alt="{{ $member['name'] }}"
                                            class="w-full h-full object-cover object-top"
                                            loading="lazy"
                                        />
                                    @else
                                        {{ $member['initials'] }}
                                    @endif
                                </div>

                                <div class="text-center w-[76px]">
                                    <p
                                        class="text-xs leading-tight truncate"
                                        :class="active === {{ $idx }} ? 'font-bold text-[#0F172A]' : 'font-medium text-slate-400'"
                                    >{{ explode(' ', $member['name'])[0] }}</p>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5 leading-tight">
                                        {{ mb_substr($member['position'], 0, 14) }}{{ mb_strlen($member['position']) > 14 ? '…' : '' }}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>

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
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Kata Mereka
                        Tentang Kami</h2>
                    <p class="text-sm text-slate-500">Membaca ulasan kepuasan dan review langsung dari mitra kerja sama
                        kami.</p>
                </div>

                <!-- Grid (Simple Flat Grid Layout, anti slop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($testimonials as $testimonial)
                        <div x-data="{ shown: false }" x-intersect.margin.-10%="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="bg-slate-50 rounded-lg border border-slate-200 p-6 flex flex-col justify-between space-y-6 transition-all duration-500">
                            <p class="text-xs text-slate-600 italic leading-relaxed">
                                "{{ $testimonial->message }}"
                            </p>

                            <div class="flex items-center gap-4 border-t border-slate-200 pt-4">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 uppercase text-xs">
                                    @if ($testimonial->getFirstMediaUrl('photo'))
                                        <img src="{{ $testimonial->getFirstMediaUrl('photo') }}"
                                            alt="{{ $testimonial->name }}" class="w-full h-full object-cover" />
                                    @else
                                        {{ substr($testimonial->name, 0, 2) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-primary truncate">{{ $testimonial->name }}</h4>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $testimonial->position }}
                                        at {{ $testimonial->company }}</p>

                                    <div class="flex items-center gap-0.5 mt-1.5 text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-lucide-star
                                                class="w-3 h-3 {{ $i <= $testimonial->rating ? 'fill-current' : 'text-slate-300' }}" />
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
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary tracking-tight font-sans">Frequently
                        Asked Questions</h2>
                    <p class="text-sm text-slate-500">Jawaban atas pertanyaan-pertanyaan yang sering ditanyakan oleh
                        klien baru.</p>
                </div>

                <!-- Accordion (Alpine) -->
                <div class="space-y-4" x-data="{ activeIndex: null }">
                    @foreach ($faqs as $index => $faq)
                        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm shadow-slate-100/30 overflow-hidden transition-all duration-300 hover:shadow-md hover:shadow-slate-200/40 hover:border-slate-200"
                            :class="activeIndex === {{ $index }} ? 'ring-1 ring-accent/30 border-accent/20' : ''">
                            <button
                                @click="activeIndex = (activeIndex === {{ $index }} ? null : {{ $index }})"
                                class="w-full flex items-center justify-between px-6 py-5 text-left font-bold text-sm md:text-base text-primary hover:text-accent transition-colors focus:outline-none group">
                                <span class="pr-4">{{ $faq->question }}</span>
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-accent group-hover:bg-accent/5 transition-all duration-300"
                                    :class="activeIndex === {{ $index }} ? 'bg-accent/10 text-accent' : ''">
                                    <x-lucide-chevron-down class="w-4 h-4 transition-transform duration-300"
                                        ::class="activeIndex === {{ $index }} ? 'rotate-180' : ''" />
                                </div>
                            </button>

                            <div x-show="activeIndex === {{ $index }}" x-collapse
                                class="px-6 pb-6 text-xs md:text-sm text-slate-500 leading-relaxed border-t border-slate-50 pt-4 bg-slate-50/20"
                                style="display: none;">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 10. CTA Kontak / Hubungi Kami (BAB 5, Section 9) -->
    <section id="contact"
        class="py-20 md:py-28 bg-slate-950 text-white relative overflow-hidden border-t border-slate-900">
        <!-- Background Grid Effect -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-35">
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-16 items-start">
            <!-- Left Info column (2 cols) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-3">
                    <span class="text-xs font-bold text-accent uppercase tracking-widest">Hubungi Kami</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight">Mulai
                        Konsultasi IT Gratis Sekarang</h2>
                    <p class="text-sm text-slate-400 leading-relaxed">Punya rencana proyek digital atau butuh bantuan
                        siber? Tuliskan kendala Anda dan tim developer kami siap mendampingi.</p>
                </div>

                <div class="space-y-4 text-sm text-slate-300">
                    <div class="flex items-center gap-4 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-map-pin class="w-5 h-5" />
                        </div>
                        <span class="leading-relaxed">{{ $settings['address'] }}</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-phone class="w-5 h-5" />
                        </div>
                        <span>{{ $settings['phone'] }}</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-accent group-hover:border-accent transition-colors duration-300">
                            <x-lucide-mail class="w-5 h-5" />
                        </div>
                        <span>{{ $settings['email'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Form column (3 cols) -->
            <div
                class="lg:col-span-3 bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 p-6 md:p-8 rounded-2xl shadow-2xl space-y-6">
                <h3 class="font-bold text-white text-base">Kirim Formulir Kontak</h3>

                <form wire:submit.prevent="submitContact" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Nama Lengkap *" />
                            @error('name')
                                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <input type="email" wire:model="email"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Email Kantor *" />
                            @error('email')
                                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <input type="text" wire:model="phone"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Nomor Telepon (Opsional)" />
                            @error('phone')
                                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <input type="text" wire:model="subject"
                                class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                                placeholder="Subjek Pesan" />
                            @error('subject')
                                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <textarea wire:model="message" rows="4"
                            class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                            placeholder="Tuliskan kendala IT atau spesifikasi project yang ingin dibuat... *"></textarea>
                        @error('message')
                            <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                            class="w-full py-3.5 bg-accent hover:bg-accent/90 text-white font-bold rounded-xl text-xs transition-all duration-300 shadow-md shadow-accent/10 hover:shadow-accent/25 hover:scale-[1.01] focus:outline-none">
                            <span wire:loading.remove wire:target="submitContact">Kirim Pesan</span>
                            <span wire:loading wire:target="submitContact"
                                class="flex items-center justify-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                <span>Mengirim...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (!empty($settings['google_maps_embed']))
            <div class="max-w-7xl mx-auto px-6 mt-12 relative z-10">
                <div class="rounded-2xl overflow-hidden border border-slate-800/80 shadow-2xl w-full h-[350px] relative [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:border-0">
                    {!! $settings['google_maps_embed'] !!}
                </div>
            </div>
        @endif
    </section>

    <!-- 11. Footer (BAB 5, Section 10) -->
    <footer class="bg-slate-950 text-slate-400 text-xs py-16 border-t border-slate-900 relative">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <!-- Company Info -->
            <div class="space-y-4">
                <a href="#"
                    class="flex items-center gap-2 font-extrabold text-white text-lg tracking-wider transition-transform duration-300 hover:scale-[1.02]">
                    @if ($settings['logo'])
                        <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] }}"
                            class="h-9 max-w-[150px] object-contain" />
                    @else
                        <x-lucide-terminal class="w-5 h-5 text-accent" />
                        <span>TECHIRA</span>
                    @endif
                </a>
                <p class="leading-relaxed mt-2 text-slate-500">Solusi teknologi software, cloud architecture, devops,
                    dan jaringan enterprise terpercaya di Indonesia.</p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="font-bold text-white text-sm">Tautan Cepat</h4>
                <ul class="space-y-3 text-slate-500 font-medium">
                    <li><a href="#"
                            class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right
                                class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Home</span></a>
                    </li>
                    <li><a href="#about"
                            class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right
                                class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Tentang
                                Kami</span></a></li>
                    <li><a href="#services"
                            class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right
                                class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Layanan
                                Jasa</span></a></li>
                    <li><a href="#products"
                            class="hover:text-white transition-colors flex items-center gap-1.5 group"><x-lucide-chevron-right
                                class="w-3 h-3 text-slate-600 group-hover:text-accent transition-colors" /><span>Produk
                                SaaS</span></a></li>
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
                    <a href="{{ $settings['social_facebook'] }}" target="_blank"
                        class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-facebook
                            class="w-4 h-4" /></a>
                    <a href="{{ $settings['social_instagram'] }}" target="_blank"
                        class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-instagram
                            class="w-4 h-4" /></a>
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank"
                        class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-linkedin
                            class="w-4 h-4" /></a>
                    <a href="{{ $settings['youtube_url'] }}" target="_blank"
                        class="p-2 bg-slate-900 border border-slate-800/80 hover:border-accent/40 hover:text-accent rounded-xl transition-all duration-300"><x-lucide-youtube
                            class="w-4 h-4" /></a>
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
