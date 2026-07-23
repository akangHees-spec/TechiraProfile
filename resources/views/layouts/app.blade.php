<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- SortableJS for Drag and Drop sorting -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <style>
        .sidebar-nav::-webkit-scrollbar { width: 0px; background: transparent; }
        .sidebar-nav { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>

<body class="font-sans antialiased bg-[#F8FAFC] text-[#0F172A]" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Backdrop (Mobile) -->
    <div x-show="sidebarOpen" x-transition:opacity @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/40 md:hidden"></div>

    <!-- Left Fixed Sidebar -->
    <aside x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 bottom-0 left-0 z-50 flex flex-col w-64 bg-[#0F1B3D] text-white border-r border-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0">
        <!-- Logo Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 font-bold text-lg tracking-wider text-white">
                <img src="{{ asset('images/images.jpg') }}" alt="Techira Logo" class="h-8 w-auto object-contain rounded" />
                <span>TECHIRA</span>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white md:hidden">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="sidebar-nav flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-layout-dashboard class="w-4 h-4" />
                <span>Dashboard</span>
            </a>

            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold tracking-wider text-slate-500 uppercase">Konten Utama</p>
            </div>

            <!-- Categories -->
            <a href="{{ route('admin.categories') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.categories*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-folder-open class="w-4 h-4" />
                <span>Kategori</span>
            </a>

            <!-- Products -->
            <a href="{{ route('admin.products') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-shopping-bag class="w-4 h-4" />
                <span>Produk</span>
            </a>

            <!-- Services -->
            <a href="{{ route('admin.services') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.services*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-briefcase class="w-4 h-4" />
                <span>Layanan Jasa</span>
            </a>

            <!-- Blog -->
            <a href="{{ route('admin.posts') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.posts*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-newspaper class="w-4 h-4" />
                <span>Blog / Artikel</span>
            </a>

            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold tracking-wider text-slate-500 uppercase">Komponen Web</p>
            </div>

            <!-- Sliders -->
            <a href="{{ route('admin.sliders') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.sliders*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-sliders class="w-4 h-4" />
                <span>Slider Hero</span>
            </a>

            <!-- Page Sections -->
            <a href="{{ route('admin.sections') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.sections*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-layers class="w-4 h-4" />
                <span>Konten Halaman</span>
            </a>

            <!-- Navigation Menu (Navbar) -->
            <a href="{{ route('admin.navbar') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.navbar*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-menu class="w-4 h-4" />
                <span>Menu Navigasi</span>
            </a>

            <!-- Testimonials -->
            <a href="{{ route('admin.testimonials') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.testimonials*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-message-square-quote class="w-4 h-4" />
                <span>Testimoni</span>
            </a>

            <!-- Team Members -->
            <a href="{{ route('admin.team') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.team*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-users class="w-4 h-4" />
                <span>Tim Kami</span>
            </a>

            <!-- Partners -->
            <a href="{{ route('admin.partners') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.partners*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-handshake class="w-4 h-4" />
                <span>Partner</span>
            </a>

            <!-- FAQs -->
            <a href="{{ route('admin.faqs') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.faqs*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-help-circle class="w-4 h-4" />
                <span>FAQ</span>
            </a>

            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold tracking-wider text-slate-500 uppercase">Administrasi</p>
            </div>

            <!-- Contact Messages -->
            <a href="{{ route('admin.messages') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.messages*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-mail class="w-4 h-4" />
                <span>Pesan Masuk</span>
            </a>

            <!-- Settings -->
            <a href="{{ route('admin.settings') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 border-l-4 border-accent text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <x-lucide-settings class="w-4 h-4" />
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Right Side Container (Topbar + Content) -->
    <div class="md:ml-64 flex flex-col min-h-screen">

        <!-- Sticky Topbar (Enhanced with shadow-sm and border-b border-slate-200) -->
        <header
            class="sticky top-0 z-20 flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200/80 shadow-sm shadow-slate-100">
            <!-- Left Side: Burger Menu & Breadcrumbs -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-1 rounded-lg text-slate-500 hover:bg-slate-100 md:hidden">
                    <x-lucide-menu class="w-6 h-6" />
                </button>

                <nav class="flex text-sm font-medium text-slate-500">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-1.5 hover:text-accent transition-colors">
                                <x-lucide-home class="w-4 h-4" />
                                <span>Admin</span>
                            </a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            <!-- Right Side: User Dropdown -->
            <div class="flex items-center gap-4">
                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 focus:outline-none">
                        <!-- Avatar with primary color ring -->
                        <div
                            class="w-8 h-8 rounded-full bg-slate-200 border-2 border-[#0F1B3D] flex items-center justify-center font-bold text-slate-700 uppercase text-xs">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <x-lucide-chevron-down class="w-4 h-4 text-slate-500 transition-transform duration-250"
                            x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-lg shadow-lg py-1.5 z-30"
                        style="display: none;">
                        <!-- User Name & Email Header -->
                        <div class="px-4 py-2 border-b border-slate-100 bg-slate-50/50 rounded-t-lg">
                            <p class="text-xs font-bold text-primary truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-danger hover:bg-slate-50 text-left">
                                <x-lucide-log-out class="w-4 h-4" />
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content Area -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    @livewireScripts
</body>

</html>
