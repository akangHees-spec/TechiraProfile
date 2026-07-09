<div class="min-h-screen bg-slate-950 text-slate-300" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <x-site-navbar :settings="$settings" :navItems="$navItems" activePage="blog" />

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-6 py-10 md:py-14">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8">
            <a href="/" class="hover:text-accent transition-colors">Home</a>
            <x-lucide-chevron-right class="w-3 h-3 flex-shrink-0" />
            <a href="{{ route('blog.index') }}" class="hover:text-accent transition-colors">Blog</a>
            <x-lucide-chevron-right class="w-3 h-3 flex-shrink-0" />
            <span class="text-slate-500 truncate max-w-[200px]">{{ $post->title }}</span>
        </nav>

        <!-- Article Header -->
        <header class="mb-8">
            <!-- Category badge -->
            <div class="mb-4">
                <span class="inline-block text-[11px] font-bold text-accent uppercase tracking-widest bg-accent/10 px-3 py-1 rounded-full">
                    Artikel
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight leading-tight mb-5">
                {{ $post->title }}
            </h1>

            <!-- Meta info -->
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pb-6 border-b border-slate-800">
                <span class="flex items-center gap-1.5">
                    <x-lucide-calendar class="w-3.5 h-3.5" />
                    {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : '' }}
                </span>
                @php
                    $wordCount = str_word_count(strip_tags($post->content));
                    $readTime = max(1, round($wordCount / 200));
                @endphp
                <span class="flex items-center gap-1.5">
                    <x-lucide-clock class="w-3.5 h-3.5" />
                    {{ $readTime }} menit baca
                </span>
            </div>
        </header>

        <!-- Cover Image -->
        @if ($post->getFirstMediaUrl('cover'))
            <figure class="w-full rounded-2xl overflow-hidden bg-slate-100 mb-10 shadow-md border border-slate-100">
                <img
                    src="{{ $post->getFirstMediaUrl('cover') }}"
                    alt="{{ $post->title }}"
                    class="w-full h-auto max-h-[420px] object-cover"
                />
            </figure>
        @endif

        <!-- Article Content -->
        <div class="blog-content text-[15px] leading-[1.85] text-slate-300 mb-12">
            {!! $post->content !!}
        </div>

        <style>
            .blog-content { font-family: 'Inter', sans-serif; }
            .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
                color: #f1f5f9;
                font-weight: 800;
                line-height: 1.3;
                margin-top: 2em;
                margin-bottom: 0.6em;
                letter-spacing: -0.02em;
            }
            .blog-content h1 { font-size: 1.9rem; }
            .blog-content h2 {
                font-size: 1.4rem;
                padding-bottom: 0.5em;
                border-bottom: 1px solid #1e293b;
            }
            .blog-content h3 { font-size: 1.15rem; color: #e2e8f0; }
            .blog-content h4 { font-size: 1rem; color: #cbd5e1; }
            .blog-content p {
                margin-top: 1em;
                margin-bottom: 1em;
                line-height: 1.85;
                color: #94a3b8;
            }
            .blog-content a {
                color: var(--color-accent, #3b82f6);
                text-decoration: underline;
                text-underline-offset: 3px;
                font-weight: 500;
            }
            .blog-content a:hover { opacity: 0.8; }
            .blog-content ul, .blog-content ol {
                padding-left: 1.4em;
                margin-top: 0.8em;
                margin-bottom: 0.8em;
            }
            .blog-content ul { list-style-type: disc; }
            .blog-content ol { list-style-type: decimal; }
            .blog-content li {
                margin-top: 0.4em;
                margin-bottom: 0.4em;
                color: #94a3b8;
                line-height: 1.7;
            }
            .blog-content li::marker { color: var(--color-accent, #3b82f6); }
            .blog-content blockquote {
                border-left: 4px solid var(--color-accent, #3b82f6);
                padding: 1em 1.5em;
                background: #0f172a;
                border-radius: 0 0.5rem 0.5rem 0;
                font-style: italic;
                color: #64748b;
                margin: 2em 0;
                font-size: 1.05em;
            }
            .blog-content img {
                border-radius: 0.75rem;
                margin: 2em auto;
                max-width: 100%;
                height: auto;
                display: block;
                border: 1px solid #1e293b;
                box-shadow: 0 2px 16px rgba(0,0,0,0.3);
            }
            .blog-content pre {
                background: #0f172a;
                border: 1px solid #1e293b;
                border-radius: 0.75rem;
                padding: 1.25em;
                overflow-x: auto;
                margin: 1.5em 0;
                font-size: 0.875em;
            }
            .blog-content code {
                background: #1e293b;
                border: 1px solid #334155;
                padding: 0.15em 0.45em;
                border-radius: 0.25rem;
                font-size: 0.875em;
                color: #f87171;
                font-family: ui-monospace, monospace;
            }
            .blog-content pre code {
                background: none;
                border: none;
                padding: 0;
                color: #e2e8f0;
                font-size: 0.9em;
            }
            .blog-content strong { color: #e2e8f0; font-weight: 700; }
            .blog-content em { color: #94a3b8; }
            .blog-content hr {
                border-color: #1e293b;
                margin: 2.5em 0;
            }
            .blog-content table {
                width: 100%;
                border-collapse: collapse;
                margin: 1.5em 0;
                font-size: 0.875em;
            }
            .blog-content th {
                background: #0f172a;
                padding: 0.75em 1em;
                text-align: left;
                font-weight: 700;
                color: #e2e8f0;
                border: 1px solid #1e293b;
            }
            .blog-content td {
                padding: 0.65em 1em;
                border: 1px solid #1e293b;
                color: #94a3b8;
            }
        </style>

        <!-- Footer row: back + time -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-800 mb-12">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-accent transition-colors">
                <x-lucide-arrow-left class="w-4 h-4" />
                Kembali ke Blog
            </a>
            <span class="text-xs text-slate-500">
                Diterbitkan {{ $post->published_at ? $post->published_at->diffForHumans() : '' }}
            </span>
        </div>

        <!-- Related Articles -->
        @if ($recentPosts->isNotEmpty())
            <section class="border-t border-slate-800 pt-10">
                <h3 class="text-sm font-bold text-slate-200 mb-5 flex items-center gap-2">
                    <x-lucide-layout-grid class="w-4 h-4 text-accent" />
                    Artikel Lainnya
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($recentPosts as $recent)
                        <a href="{{ route('blog.show', $recent->slug) }}"
                            class="group block bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden hover:border-slate-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                            <div class="aspect-[16/9] overflow-hidden bg-slate-800">
                                @if ($recent->getFirstMediaUrl('cover'))
                                    <img src="{{ $recent->getFirstMediaUrl('cover') }}" alt="{{ $recent->title }}"
                                        class="w-full h-full object-cover transition-transform duration-400 group-hover:scale-[1.05]" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                                        <x-lucide-newspaper class="w-8 h-8 stroke-[1.5]" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-3.5 space-y-1">
                                <div class="text-[10px] text-accent font-semibold">
                                    {{ $recent->published_at ? $recent->published_at->translatedFormat('d M Y') : '' }}
                                </div>
                                <h4 class="text-xs font-bold text-slate-300 group-hover:text-accent transition-colors line-clamp-2 leading-snug">
                                    {{ $recent->title }}
                                </h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-950 py-7 mt-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $settings['company_name'] }}. All rights reserved.</p>
            <div class="flex gap-5">
                @if ($settings['social_facebook'] && $settings['social_facebook'] !== '#')
                    <a href="{{ $settings['social_facebook'] }}" target="_blank" class="hover:text-accent transition-colors">Facebook</a>
                @endif
                @if ($settings['social_instagram'] && $settings['social_instagram'] !== '#')
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" class="hover:text-accent transition-colors">Instagram</a>
                @endif
                @if ($settings['social_linkedin'] && $settings['social_linkedin'] !== '#')
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="hover:text-accent transition-colors">LinkedIn</a>
                @endif
            </div>
        </div>
    </footer>

</div>
