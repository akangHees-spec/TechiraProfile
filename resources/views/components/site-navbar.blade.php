@props([
    'settings'   => [],
    'navItems'   => [],
    'isLanding'  => false,
    'activePage' => '',
])

@php
    // Route map: jika di halaman non-landing, anchor #services dll di-redirect ke route-nya
    $routeMap = [
        '#services' => route('services.index'),
        '#products' => route('products.index'),
        '/blog'     => route('blog.index'),
    ];

    // Active-page map: tentukan item mana yang "aktif" (highlight underline)
    $activeUrlMap = [
        'services' => ['#services', route('services.index')],
        'products' => ['#products', route('products.index')],
        'blog'     => ['/blog', route('blog.index')],
    ];

    $resolveUrl = function($url) use ($isLanding, $routeMap) {
        if ($isLanding) {
            return $url;
        }
        // Punya route khusus → pakai route
        if (isset($routeMap[$url])) {
            return $routeMap[$url];
        }
        // Anchor biasa (#about, #team, dll) → prefix dengan /
        if (str_starts_with($url, '#')) {
            return $url === '#' ? '/' : '/' . $url;
        }
        return $url;
    };

    $isActive = function($url) use ($activePage, $activeUrlMap) {
        if (!$activePage) return false;
        $urlsForPage = $activeUrlMap[$activePage] ?? [];
        return in_array($url, $urlsForPage);
    };

    $headerBase = $isLanding ? 'fixed top-0 inset-x-0' : 'sticky top-0';
    $normalCls  = $isLanding ? 'bg-transparent py-6' : 'bg-[#0F1B3D] py-5';
    $contactUrl = $isLanding ? '#contact' : '/#contact';
    $homeUrl    = $isLanding ? '#' : '/';
@endphp

<header
    x-data="{ open: false }"
    :class="scrolled ? 'bg-[#0F1B3D] border-b border-slate-800/80 shadow-lg py-4' : '{{ $normalCls }}'"
    class="{{ $headerBase }} z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ $homeUrl }}" class="flex items-center gap-2.5 font-extrabold text-xl tracking-wider text-white transition-transform duration-200 hover:scale-[1.01]">
            @if ($settings['logo'] ?? null)
                <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] ?? '' }}" class="h-8 w-auto object-contain rounded" />
            @else
                <x-lucide-terminal class="w-6 h-6 text-[#2563EB]" style="stroke-width: 2;" />
            @endif
            <span>{{ $settings['company_name'] ?? '' }}</span>
        </a>

        {{-- Desktop Menu --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
            @foreach($navItems as $item)
                @php
                    $resolvedUrl = $resolveUrl($item->url ?? '#');
                    $active = $isActive($item->url ?? '#');
                @endphp
                <a href="{{ $resolvedUrl }}" class="{{ $active ? 'relative py-1.5 text-[#2563EB]' : 'relative py-1.5 hover:text-white transition-colors group' }}">
                    <span>{{ $item->label }}</span>
                    @if($active)
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-[#2563EB]"></span>
                    @else
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#2563EB] transition-all duration-200 group-hover:w-full"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- CTA Button --}}
        <div class="hidden md:flex items-center gap-4">
            <a href="{{ $contactUrl }}" class="px-5 py-2.5 bg-[#2563EB] hover:bg-[#3B82F6] text-white font-bold text-xs rounded-lg shadow-sm transition-all duration-150">Hubungi Kami</a>
        </div>

        {{-- Hamburger Mobile Button --}}
        <div class="md:hidden">
            <button @click="open = true" class="text-white hover:text-[#2563EB] focus:outline-none p-1">
                <x-lucide-menu class="w-6 h-6" style="stroke-width: 2;" />
            </button>
        </div>

        {{-- Responsive Mobile Menu Drawer (Slide from right) --}}
        <div 
            x-show="open" 
            class="fixed inset-0 z-50 md:hidden" 
            style="display: none;"
        >
            <!-- Overlay Backdrops -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"
                @click="open = false"
            ></div>

            <!-- Drawer Body -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed top-0 right-0 bottom-0 w-72 bg-[#0F1B3D] border-l border-slate-800 p-6 flex flex-col justify-between shadow-2xl"
            >
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <a href="{{ $homeUrl }}" class="flex items-center gap-2 font-bold text-base text-white">
                            @if ($settings['logo'] ?? null)
                                <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] ?? '' }}" class="h-6 w-auto object-contain rounded" />
                            @else
                                <x-lucide-terminal class="w-5 h-5 text-[#2563EB]" />
                            @endif
                            <span>TECHIRA</span>
                        </a>
                        <button @click="open = false" class="text-slate-400 hover:text-white p-1">
                            <x-lucide-x class="w-5 h-5" style="stroke-width: 2;" />
                        </button>
                    </div>

                    <!-- Navigation list -->
                    <nav class="flex flex-col gap-1">
                        @foreach($navItems as $item)
                            @php
                                $resolvedUrl = $resolveUrl($item->url ?? '#');
                                $active = $isActive($item->url ?? '#');
                            @endphp
                            <a @click="open = false" href="{{ $resolvedUrl }}"
                                class="flex items-center min-h-[44px] px-3 rounded-lg text-sm transition-colors {{ $active ? 'bg-slate-800 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white font-semibold' }}">
                                {{ $item->label }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="pt-4 border-t border-slate-800">
                    <a @click="open = false" href="{{ $contactUrl }}" class="flex items-center justify-center min-h-[44px] w-full bg-[#2563EB] hover:bg-[#3B82F6] text-white text-xs font-bold rounded-lg transition-colors">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>

    </div>
</header>
