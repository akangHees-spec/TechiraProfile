<div class="min-h-screen bg-slate-950 text-slate-300 selection:bg-accent selection:text-white" x-data="{ scrolled: false, activeCategory: 'all' }" @scroll.window="scrolled = (window.pageYOffset > 20)" x-init="window.addEventListener('redirect-to', e => { window.open(e.detail.url, '_blank'); });">
    
    <x-site-navbar :settings="$settings" :navItems="$navItems" activePage="services" />

    <!-- Main Content Area -->
    <main class="py-8 max-w-7xl mx-auto px-6">
        
        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-4 border-b border-slate-900 pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">Layanan Kami</h1>
                <p class="text-slate-400 text-xs md:text-sm mt-1 max-w-xl">Pilih layanan TI profesional dan tepercaya yang dirancang khusus untuk optimasi bisnis Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Sidebar Filter -->
            <aside class="lg:col-span-3 space-y-6">
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-5 sticky top-24">
                    <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-4 flex items-center justify-between">
                        <span>Filter Kategori</span>
                        <x-lucide-filter class="w-3.5 h-3.5 text-slate-500" />
                    </h3>
                    
                    <div class="space-y-1">
                        <button @click="activeCategory = 'all'" 
                                :class="activeCategory === 'all' ? 'bg-accent/15 border-accent text-white' : 'border-transparent text-slate-400 hover:text-white'" 
                                class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-lg border text-left transition-all">
                            <span>Semua Layanan</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-800 text-[10px]">{{ $services->count() }}</span>
                        </button>
                        
                        @foreach($categories as $cat)
                            <button @click="activeCategory = '{{ $cat->slug }}'" 
                                    :class="activeCategory === '{{ $cat->slug }}' ? 'bg-accent/15 border-accent text-white' : 'border-transparent text-slate-400 hover:text-white'" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-lg border text-left transition-all">
                                <span>{{ $cat->name }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-slate-800 text-[10px]">
                                    {{ $services->where('category_id', $cat->id)->count() }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Services Grid -->
            <section class="lg:col-span-9">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse ($services as $service)
                        <div x-show="activeCategory === 'all' || activeCategory === '{{ $service->category?->slug }}'" 
                             x-transition.fade
                             class="group bg-slate-900/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/80 hover:shadow-xl relative">
                            
                            <!-- Top Info -->
                            <div class="flex items-center justify-between mb-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <span>{{ $service->category?->name ?? 'Layanan' }}</span>
                                @if($service->is_featured)
                                    <span class="text-indigo-400 text-[9px] bg-indigo-500/10 border border-indigo-500/20 rounded px-1.5 py-0.5">Rekomendasi</span>
                                @endif
                            </div>

                            <!-- Cover Image or Icon -->
                            <div class="w-full h-32 md:h-40 relative rounded-lg bg-slate-950 overflow-hidden mb-4 border border-slate-800/40">
                                @if ($service->getFirstMediaUrl('image'))
                                    <img src="{{ $service->getFirstMediaUrl('image') }}"
                                        alt="{{ $service->name }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.04]" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800/60 flex items-center justify-center text-accent group-hover:scale-105 transition-transform duration-500">
                                            <x-dynamic-component :component="'lucide-' . ($service->icon ?: 'activity')" class="w-6 h-6" />
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Info Content -->
                            <div class="space-y-1.5 mb-4">
                                <h3 class="font-bold text-white text-sm md:text-base group-hover:text-accent transition-colors line-clamp-1 leading-snug">
                                    {{ $service->name }}
                                </h3>
                                @if($service->short_description)
                                    <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed">
                                        {{ $service->short_description }}
                                    </p>
                                @endif
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-3 border-t border-slate-800/60 flex items-center justify-between gap-3 mt-auto">
                                <span class="text-[10px] text-slate-500 font-semibold uppercase">Konsultasi</span>
                                <a href="{{ route('services.show', $service->slug) }}" class="flex items-center gap-1 px-3 py-1.5 bg-accent hover:bg-accent/90 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-lg transition-all">
                                    <span>Detail</span>
                                    <x-lucide-arrow-right class="w-3 h-3" />
                                </a>
                            </div>
                            
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <div class="w-16 h-16 bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-800">
                                <x-lucide-activity class="w-8 h-8 text-slate-500" />
                            </div>
                            <h3 class="text-sm font-bold text-white">Belum Ada Layanan</h3>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 pt-10 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <a href="/" class="inline-flex items-center gap-2 font-black text-base tracking-tight text-white mb-3">
                @if ($settings['logo'])
                    <img src="{{ $settings['logo'] }}" alt="{{ $settings['company_name'] }}" class="h-6 object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all" />
                @else
                    <x-lucide-terminal class="w-4 h-4 text-accent" />
                    <span>{{ strtoupper($settings['company_name']) }}</span>
                @endif
            </a>
            <p class="text-[11px] text-slate-500 mb-4 max-w-sm mx-auto font-medium">Solusi teknologi terpercaya untuk mendukung pertumbuhan bisnis Anda di era digital.</p>
            <div class="flex justify-center gap-4 text-slate-600 mb-6">
                @if($settings['social_facebook'] != '#') <a href="{{ $settings['social_facebook'] }}" class="hover:text-accent transition-colors"><x-lucide-facebook class="w-3.5 h-3.5"/></a> @endif
                @if($settings['social_instagram'] != '#') <a href="{{ $settings['social_instagram'] }}" class="hover:text-accent transition-colors"><x-lucide-instagram class="w-3.5 h-3.5"/></a> @endif
                @if($settings['social_linkedin'] != '#') <a href="{{ $settings['social_linkedin'] }}" class="hover:text-accent transition-colors"><x-lucide-linkedin class="w-3.5 h-3.5"/></a> @endif
            </div>
            <div class="pt-4 border-t border-slate-900 text-[9px] text-slate-600 font-medium uppercase tracking-wider">
                &copy; {{ date('Y') }} {{ $settings['company_name'] }}. All rights reserved.
            </div>
        </div>
    </footer>
</div>
