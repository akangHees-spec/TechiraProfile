<div class="min-h-screen bg-slate-950 text-slate-300 selection:bg-accent selection:text-white" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)" x-init="window.addEventListener('redirect-to', e => { window.open(e.detail.url, '_blank'); });">
    
    <x-site-navbar :settings="$settings" :navItems="$navItems" activePage="services" />

    <main class="py-8 max-w-7xl mx-auto px-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 mb-6 uppercase tracking-wider">
            <a href="/" class="hover:text-accent transition-colors">Home</a>
            <x-lucide-chevron-right class="w-3 h-3 text-slate-600" />
            <a href="{{ route('services.index') }}" class="hover:text-accent transition-colors">Layanan</a>
            <x-lucide-chevron-right class="w-3 h-3 text-slate-600" />
            <span class="text-white truncate">{{ $service->name }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            
            <!-- Left Side: Icon -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-8 flex items-center justify-center relative shadow-lg shadow-black/25">
                <div class="w-28 h-28 rounded-full bg-slate-850 flex items-center justify-center border-4 border-slate-700 shadow-inner">
                    <x-dynamic-component :component="'lucide-' . ($service->icon ?: 'activity')" class="w-12 h-12 text-accent" />
                </div>
            </div>

            <!-- Right Side: Details -->
            <div class="flex flex-col space-y-6">
                <div>
                    <span class="text-xs font-bold text-accent uppercase tracking-wider">
                        {{ $service->category?->name ?? 'Layanan' }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-white mt-1 leading-snug">{{ $service->name }}</h1>
                </div>

                @if($service->short_description)
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $service->short_description }}</p>
                @endif

                <!-- Whatsapp CTA -->
                <div class="pt-4">
                    <button wire:click="trackWa" class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-[#25D366] hover:bg-[#128C7E] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-[#25D366]/20 transition-all hover:-translate-y-0.5 focus:outline-none">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                        <span>Hubungi via WhatsApp</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Sections -->
        <div class="mt-12 border-t border-slate-900 pt-8 space-y-8">
            <!-- Features -->
            @if($service->features->count() > 0)
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-5">
                    <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-accent" /> Fitur Unggulan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($service->features as $feature)
                            <div class="flex items-start gap-3 p-3 bg-slate-950/40 rounded-lg border border-slate-850/50">
                                <div class="w-6 h-6 rounded-full bg-slate-900 flex items-center justify-center text-accent shrink-0">
                                    <x-dynamic-component :component="'lucide-' . ($feature->icon ?: 'check')" class="w-3.5 h-3.5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-xs">{{ $feature->title }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Full Description -->
            @if($service->description)
                <div class="prose prose-sm prose-invert max-w-none">
                    <h3 class="text-base font-bold text-white border-b border-slate-800 pb-2 mb-4">Detail Layanan</h3>
                    <div class="whitespace-pre-line text-slate-400 leading-relaxed text-sm">{{ $service->description }}</div>
                </div>
            @endif
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
