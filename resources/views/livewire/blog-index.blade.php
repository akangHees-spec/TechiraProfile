<div class="min-h-screen bg-slate-950 text-slate-300 selection:bg-accent selection:text-white" x-data="{ scrolled: false }"
    @scroll.window="scrolled = (window.pageYOffset > 20)">

    <x-site-navbar :settings="$settings" :navItems="$navItems" activePage="blog" />

    <!-- Main Content Area -->
    <main class="py-12 max-w-7xl mx-auto px-6">

        <!-- Header Section -->
        <div
            class="mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-6 border-b border-slate-900 pb-8">
            <div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight">Blog & Artikel</h1>
                <p class="text-slate-400 text-xs md:text-sm mt-2 max-w-xl">Ikuti artikel, informasi terbaru, tips, dan
                    wawasan teknologi terhangat dari tim ahli kami.</p>
            </div>

            <!-- Search bar -->
            <div class="w-full md:w-80 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full pl-9 pr-4 py-2.5 border border-slate-800 rounded-xl text-xs bg-slate-900/40 text-white placeholder-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-300"
                    placeholder="Cari artikel..." />
            </div>
        </div>

        @if ($posts->count() > 0)
            <!-- Standard Grid of all posts -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                    <article
                        class="group bg-slate-900/30 border border-slate-900 hover:border-slate-800 rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-black/30">
                        <div>
                            <!-- Cover Image -->
                            <a href="{{ route('blog.show', $post->slug) }}"
                                class="block aspect-[16/10] overflow-hidden bg-slate-950 border-b border-slate-900 relative">
                                @if ($post->getFirstMediaUrl('cover'))
                                    <img src="{{ $post->getFirstMediaUrl('cover') }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103" />
                                @else
                                    <div
                                        class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-slate-900/60">
                                        <x-lucide-newspaper class="w-12 h-12 mb-2 stroke-[1.5]" />
                                    </div>
                                @endif
                                <!-- Date Overlay -->
                                <div
                                    class="absolute bottom-3 left-3 bg-slate-950/85 backdrop-blur px-2.5 py-1 rounded text-[10px] text-accent font-bold">
                                    {{ $post->published_at ? $post->published_at->translatedFormat('d M Y') : '' }}
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="p-6 space-y-3">
                                <h2
                                    class="text-lg font-bold text-white leading-snug group-hover:text-accent transition-colors">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h2>
                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-3">
                                    {{ Str::limit(strip_tags($post->content), 120) }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer Link -->
                        <div class="px-6 pb-6 pt-2">
                            <a href="{{ route('blog.show', $post->slug) }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-accent hover:text-white transition-colors">
                                <span>Baca Selengkapnya</span>
                                <x-lucide-arrow-right
                                    class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($posts->hasPages())
                <div class="mt-16 pt-8 border-t border-slate-900">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="py-20 text-center max-w-md mx-auto">
                <div
                    class="w-16 h-16 bg-slate-900/50 rounded-2xl flex items-center justify-center text-slate-650 mx-auto mb-4 border border-slate-800">
                    <x-lucide-newspaper class="w-8 h-8 stroke-[1.5]" />
                </div>
                <h3 class="text-lg font-bold text-white">Belum Ada Artikel</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Tidak ditemukan artikel yang dipublikasikan. Coba masukkan kata kunci pencarian lain atau kembali
                    lagi nanti.
                </p>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/60 py-8 mt-20">
        <div
            class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $settings['company_name'] }}. All rights reserved.</p>
            <div class="flex gap-4">
                @if ($settings['social_facebook'] && $settings['social_facebook'] !== '#')
                    <a href="{{ $settings['social_facebook'] }}" target="_blank"
                        class="hover:text-accent transition-colors">Facebook</a>
                @endif
                @if ($settings['social_instagram'] && $settings['social_instagram'] !== '#')
                    <a href="{{ $settings['social_instagram'] }}" target="_blank"
                        class="hover:text-accent transition-colors">Instagram</a>
                @endif
                @if ($settings['social_linkedin'] && $settings['social_linkedin'] !== '#')
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank"
                        class="hover:text-accent transition-colors">LinkedIn</a>
                @endif
            </div>
        </div>
    </footer>
</div>
