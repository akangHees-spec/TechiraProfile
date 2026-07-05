<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Produk -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-neutral uppercase tracking-wider">Total Produk</p>
                <h3 class="text-3xl font-bold text-primary mt-2">{{ $totalProducts }}</h3>
            </div>
            <div class="p-3.5 bg-slate-100 rounded-lg text-primary">
                <x-lucide-shopping-bag class="w-6 h-6 text-accent" />
            </div>
        </div>

        <!-- Card 2: Total Jasa -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-neutral uppercase tracking-wider">Total Layanan Jasa</p>
                <h3 class="text-3xl font-bold text-primary mt-2">{{ $totalServices }}</h3>
            </div>
            <div class="p-3.5 bg-slate-100 rounded-lg text-primary">
                <x-lucide-briefcase class="w-6 h-6 text-accent" />
            </div>
        </div>

        <!-- Card 3: Pesan Belum Dibaca -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-neutral uppercase tracking-wider">Pesan Baru</p>
                <h3 class="text-3xl font-bold text-primary mt-2">{{ $unreadMessages }}</h3>
            </div>
            <div class="p-3.5 bg-slate-100 rounded-lg text-primary">
                <x-lucide-mail class="w-6 h-6 text-accent" />
            </div>
        </div>

        <!-- Card 4: Total Clicks WA -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-neutral uppercase tracking-wider">Klik WhatsApp</p>
                <h3 class="text-3xl font-bold text-primary mt-2">{{ $totalWaClicks }}</h3>
            </div>
            <div class="p-3.5 bg-slate-100 rounded-lg text-primary">
                <x-lucide-phone-call class="w-6 h-6 text-accent" />
            </div>
        </div>
    </div>

    <!-- Widgets Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Top 5 WhatsApp Clicks Widget -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <div>
                    <h3 class="font-bold text-primary text-base">Produk / Jasa Paling Banyak Ditanyakan</h3>
                    <p class="text-xs text-slate-500 mt-1">Berdasarkan total klik tombol tanya WhatsApp oleh pengunjung.</p>
                </div>
                <x-lucide-trending-up class="w-5 h-5 text-accent" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3 pr-4">Nama Item</th>
                            <th class="pb-3 px-4">Tipe</th>
                            <th class="pb-3 px-4">Kategori</th>
                            <th class="pb-3 pl-4 text-right">Jumlah Tanya (Klik)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($topAsked as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 pr-4 font-semibold text-primary">
                                    {{ $item['name'] }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider {{ $item['type'] === 'Produk' ? 'bg-blue-50 text-blue-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    {{ $item['category'] }}
                                </td>
                                <td class="py-3.5 pl-4 text-right font-bold text-primary">
                                    <span class="flex items-center justify-end gap-1.5 text-accent">
                                        <x-lucide-mouse-pointer-click class="w-4 h-4" />
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

        <!-- Quick Access Panel -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-primary text-base border-b border-slate-100 pb-4 mb-5 flex items-center gap-2">
                <x-lucide-rocket class="w-5 h-5 text-accent" />
                <span>Navigasi Cepat</span>
            </h3>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.categories') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-folder-open class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Kategori</span>
                </a>
                <a href="{{ route('admin.products') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-shopping-bag class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Produk</span>
                </a>
                <a href="{{ route('admin.services') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-briefcase class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Layanan</span>
                </a>
                <a href="{{ route('admin.messages') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-mail class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Pesan</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-settings class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Pengaturan</span>
                </a>
                <a href="/" target="_blank" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-lg text-center transition-colors">
                    <x-lucide-external-link class="w-5 h-5 text-accent mx-auto mb-2" />
                    <span class="text-xs font-semibold text-primary">Lihat Web</span>
                </a>
            </div>
        </div>

    </div>
</div>
