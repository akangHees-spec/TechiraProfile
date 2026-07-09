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
    $normalCls  = $isLanding ? 'bg-transparent py-6' : 'bg-slate-950 py-5';
    $contactUrl = $isLanding ? '#contact' : '/#contact';
    $homeUrl    = $isLanding ? '#' : '/';
@endphp

<header
    :class="scrolled ? 'bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/40 shadow-lg shadow-black/10 py-3.5' : '{{ $normalCls }}'"
    class="{{ $headerBase }} z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ $homeUrl }}" class="flex items-center gap-2 font-extrabold text-xl tracking-wider text-white transition-transform duration-300 hover:scale-[1.02]">
            @if ($settings['logo'] ?? null)
                <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] ?? '' }}" class="h-8 max-w-[40px] object-contain" />
            @else
                <x-lucide-terminal class="w-6 h-6 text-accent" />
            @endif
            <span>{{ $settings['company_name'] ?? '' }}</span>
        </a>

        {{-- Desktop Menu --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-400">
            @foreach($navItems as $item)
                @php
                    $resolvedUrl = $resolveUrl($item->url ?? '#');
                    $active = $isActive($item->url ?? '#');
                @endphp
                <a href="{{ $resolvedUrl }}" class="{{ $active ? 'relative py-1.5 text-white transition-colors' : 'relative py-1.5 hover:text-white transition-colors group' }}">
                    <span>{{ $item->label }}</span>
                    @if($active)
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-accent"></span>
                    @else
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- CTA --}}
        <div class="hidden md:flex items-center gap-4">
            <a href="{{ $contactUrl }}" class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white font-bold text-xs rounded-lg shadow-sm transition-all">Hubungi Kami</a>
        </div>

        {{-- Hamburger Mobile --}}
        <div class="md:hidden" x-data="{ open: false }">
            <button @click="open = !open" class="text-white hover:text-accent focus:outline-none">
                <x-lucide-menu x-show="!open" class="w-6 h-6" />
                <x-lucide-x x-show="open" class="w-6 h-6" style="display: none;" />
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute top-full left-0 right-0 mt-2 bg-slate-950/98 backdrop-blur-lg border border-slate-800 rounded-lg p-5 shadow-2xl space-y-1"
                style="display: none;">
                @foreach($navItems as $item)
                    @php
                        $resolvedUrl = $resolveUrl($item->url ?? '#');
                        $active = $isActive($item->url ?? '#');
                    @endphp
                    <a @click="open = false" href="{{ $resolvedUrl }}"
                        class="{{ $active ? 'block text-accent text-sm font-bold py-2' : 'block text-slate-300 hover:text-white text-sm font-semibold py-2' }}">
                        {{ $item->label }}
                    </a>
                @endforeach
                <div class="pt-2 border-t border-slate-800">
                    <a href="{{ $contactUrl }}" class="block w-full text-center px-4 py-2.5 bg-accent hover:bg-accent/90 text-white text-xs font-bold rounded-lg transition-colors">Hubungi Kami</a>
                </div>
            </div>
        </div>

    </div>
</header>
