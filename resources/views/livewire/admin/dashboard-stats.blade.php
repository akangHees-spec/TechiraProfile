<div>
    <!-- Stats Grid (Flat solid gradient shades from Navy-Blue, matching Login theme) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Produk (Accent: bg-[#0F172A] or #0F1B3D) -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between transition-all duration-150 hover:shadow-md">
            <div>
                <p class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Total Produk</p>
                <h3 class="text-3xl font-bold text-[#0F172A] mt-2">{{ $totalProducts }}</h3>
            </div>
            <!-- Icon container with explicit size classes and solid flat background -->
            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-[#0F1B3D] text-white flex-shrink-0">
                <x-lucide-shopping-bag class="w-5 h-5 text-white" style="stroke-width: 1.5;" />
            </div>
        </div>

        <!-- Card 2: Total Jasa (Accent: bg-[#1E3A8A]) -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between transition-all duration-150 hover:shadow-md">
            <div>
                <p class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Total Layanan Jasa</p>
                <h3 class="text-3xl font-bold text-[#0F172A] mt-2">{{ $totalServices }}</h3>
            </div>
            <!-- Icon container with explicit size classes and solid flat background -->
            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-[#1E3A8A] text-white flex-shrink-0">
                <x-lucide-briefcase class="w-5 h-5 text-white" style="stroke-width: 1.5;" />
            </div>
        </div>

        <!-- Card 3: Pesan Baru (Accent: bg-[#2563EB]) -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between transition-all duration-150 hover:shadow-md">
            <div>
                <p class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Pesan Baru</p>
                <h3 class="text-3xl font-bold text-[#0F172A] mt-2">{{ $unreadMessages }}</h3>
            </div>
            <!-- Icon container with explicit size classes and solid flat background -->
            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-[#2563EB] text-white flex-shrink-0">
                <x-lucide-mail class="w-5 h-5 text-white" style="stroke-width: 1.5;" />
            </div>
        </div>

        <!-- Card 4: Klik WhatsApp (Accent: bg-[#3B82F6]) -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between transition-all duration-150 hover:shadow-md">
            <div>
                <p class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Klik WhatsApp</p>
                <h3 class="text-3xl font-bold text-[#0F172A] mt-2">{{ $totalWaClicks }}</h3>
            </div>
            <!-- Icon container with explicit size classes and solid flat background -->
            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-[#3B82F6] text-white flex-shrink-0">
                <x-lucide-phone-call class="w-5 h-5 text-white" style="stroke-width: 1.5;" />
            </div>
        </div>
    </div>

    <!-- Widgets Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Top 5 WhatsApp Clicks Widget -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div>
                    <h3 class="font-bold text-[#0F172A] text-base">Produk / Jasa Paling Banyak Ditanyakan</h3>
                    <p class="text-xs text-[#64748B] mt-1">Berdasarkan total klik tombol tanya WhatsApp oleh pengunjung.</p>
                </div>
                <div class="w-9 h-9 flex items-center justify-center bg-[#EFF6FF] text-[#2563EB] rounded-lg">
                    <x-lucide-trending-up class="w-4.5 h-4.5" style="stroke-width: 1.5;" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider border-b border-slate-200 pb-3">
                            <th class="pb-3 pr-4 font-semibold">Nama Item</th>
                            <th class="pb-3 px-4 font-semibold">Tipe</th>
                            <th class="pb-3 px-4 font-semibold">Kategori</th>
                            <th class="pb-3 pl-4 text-right font-semibold">Jumlah Tanya (Klik)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[#64748B]">
                        @forelse ($topAsked as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 pr-4 font-semibold text-[#0F172A]">
                                    {{ $item['name'] }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item['type'] === 'Produk' ? 'bg-[#0F1B3D]/10 text-[#0F1B3D]' : 'bg-[#EFF6FF] text-[#2563EB]' }}">
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-medium">
                                    {{ $item['category'] }}
                                </td>
                                <td class="py-3.5 pl-4 text-right font-bold">
                                    <span class="inline-flex items-center gap-1.5 text-[#2563EB] bg-[#EFF6FF] px-2.5 py-1 rounded-md text-xs">
                                        <x-lucide-mouse-pointer-click class="w-3.5 h-3.5" style="stroke-width: 2;" />
                                        <span>{{ $item['clicks'] }} kali</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Belum ada data klik WhatsApp yang terekam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access Panel (GRID 2x2 Clean Tiles with Soft Blue-Tint, no large decorative icons) -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-[#0F172A] text-base border-b border-slate-100 pb-4 mb-5 flex items-center gap-2">
                <div class="w-8 h-8 flex items-center justify-center bg-[#EFF6FF] text-[#2563EB] rounded-lg">
                    <x-lucide-rocket class="w-4 h-4 text-[#2563EB]" style="stroke-width: 1.5;" />
                </div>
                <span>Navigasi Cepat</span>
            </h3>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.categories') }}" class="group p-4 bg-[#EFF6FF] hover:bg-blue-100/70 border border-blue-50/50 hover:border-blue-100 rounded-xl text-center transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm">
                    <div class="w-8 h-8 flex items-center justify-center mx-auto mb-2">
                        <x-lucide-folder-open class="w-5 h-5 text-[#2563EB] transition-transform duration-200 group-hover:scale-110" style="stroke-width: 1.5;" />
                    </div>
                    <span class="text-xs font-bold text-[#0F172A]">Kategori</span>
                </a>
                <a href="{{ route('admin.products') }}" class="group p-4 bg-[#EFF6FF] hover:bg-blue-100/70 border border-blue-50/50 hover:border-blue-100 rounded-xl text-center transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm">
                    <div class="w-8 h-8 flex items-center justify-center mx-auto mb-2">
                        <x-lucide-shopping-bag class="w-5 h-5 text-[#2563EB] transition-transform duration-200 group-hover:scale-110" style="stroke-width: 1.5;" />
                    </div>
                    <span class="text-xs font-bold text-[#0F172A]">Produk</span>
                </a>
                <a href="{{ route('admin.services') }}" class="group p-4 bg-[#EFF6FF] hover:bg-blue-100/70 border border-blue-50/50 hover:border-blue-100 rounded-xl text-center transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm">
                    <div class="w-8 h-8 flex items-center justify-center mx-auto mb-2">
                        <x-lucide-briefcase class="w-5 h-5 text-[#2563EB] transition-transform duration-200 group-hover:scale-110" style="stroke-width: 1.5;" />
                    </div>
                    <span class="text-xs font-bold text-[#0F172A]">Layanan</span>
                </a>
                <a href="{{ route('admin.messages') }}" class="group p-4 bg-[#EFF6FF] hover:bg-blue-100/70 border border-blue-50/50 hover:border-blue-100 rounded-xl text-center transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm">
                    <div class="w-8 h-8 flex items-center justify-center mx-auto mb-2">
                        <x-lucide-mail class="w-5 h-5 text-[#2563EB] transition-transform duration-200 group-hover:scale-110" style="stroke-width: 1.5;" />
                    </div>
                    <span class="text-xs font-bold text-[#0F172A]">Pesan</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="group p-4 bg-[#EFF6FF] hover:bg-blue-100/70 border border-blue-50/50 hover:border-blue-100 rounded-xl text-center transition-all duration-150 hover:-translate-y-0.5 hover:shadow-sm col-span-2">
                    <div class="w-8 h-8 flex items-center justify-center mx-auto mb-1">
                        <x-lucide-settings class="w-5 h-5 text-[#2563EB] transition-transform duration-200 group-hover:scale-110" style="stroke-width: 1.5;" />
                    </div>
                    <span class="text-xs font-bold text-[#0F172A]">Pengaturan</span>
                </a>
            </div>
        </div>

    </div>
</div>
