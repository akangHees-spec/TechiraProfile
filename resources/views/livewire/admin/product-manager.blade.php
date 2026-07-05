<div>
    <!-- Toast Notification -->
    @if (session()->has('toast'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 w-full max-w-sm p-4 bg-white border border-slate-200 rounded-lg shadow-xl"
        >
            <div class="flex-shrink-0">
                <x-lucide-check-circle class="w-6 h-6 text-success" />
            </div>
            <div class="flex-1 text-sm font-medium text-slate-900">
                {{ session('toast')['message'] }}
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-primary">Daftar Produk</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola produk software (SaaS), perangkat keras IT, dll yang dijual perusahaan.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Produk</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $productId ? 'Ubah Produk' : 'Tambah Produk Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Produk</label>
                            <input 
                                type="text" 
                                wire:model="name"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('name') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Techira HRIS"
                            />
                            @error('name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Kategori Produk</label>
                            <select 
                                wire:model="category_id"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                            >
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Harga (Rupiah - Opsional)</label>
                            <input 
                                type="number" 
                                wire:model="price"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                placeholder="Contoh: 1500000"
                            />
                            @error('price') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Urutan (Order)</label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                            />
                        </div>

                        <!-- Image File -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Gambar Produk</label>
                            <input 
                                type="file" 
                                wire:model="imageFile"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            @error('imageFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Deskripsi Singkat</label>
                            <input 
                                type="text" 
                                wire:model="short_description"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                placeholder="Teks satu baris ulasan singkat..."
                            />
                            @error('short_description') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Deskripsi Detail</label>
                            <textarea 
                                wire:model="description"
                                rows="5"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                placeholder="Tuliskan ulasan spesifik produk di sini..."
                            ></textarea>
                            @error('description') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Toggles -->
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_featured" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Produk Unggulan</span>
                            </label>
                        </div>

                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Tampilkan di Web</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Specifications -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-primary uppercase tracking-wider">Spesifikasi Tambahan</h3>
                            <button 
                                type="button" 
                                wire:click="addSpec"
                                class="flex items-center gap-1 text-xs text-accent font-semibold hover:text-accent/80 focus:outline-none"
                            >
                                <x-lucide-plus class="w-4 h-4" />
                                <span>Tambah Baris</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach ($specs as $index => $spec)
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="text" 
                                        wire:model="specs.{{ $index }}.key"
                                        class="flex-1 px-3.5 py-1.5 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                                        placeholder="Nama Spek (misal: Storage)"
                                    />
                                    <input 
                                        type="text" 
                                        wire:model="specs.{{ $index }}.value"
                                        class="flex-1 px-3.5 py-1.5 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                                        placeholder="Nilai Spek (misal: 256GB)"
                                    />
                                    <button 
                                        type="button" 
                                        wire:click="removeSpec({{ $index }})"
                                        class="p-1.5 text-danger hover:bg-slate-100 rounded-lg focus:outline-none"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                            @if (empty($specs))
                                <p class="text-xs text-slate-400">Belum ada spesifikasi ditambahkan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
                        >
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="cancel"
                            class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-sm rounded-lg transition-colors focus:outline-none"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Preview Area -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 h-fit space-y-6">
                <h2 class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Live Preview</h2>
                
                <div class="border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                    <div class="w-full aspect-[4/3] bg-slate-200 flex items-center justify-center text-slate-400 overflow-hidden">
                        @if ($imageFile)
                            <img src="{{ $imageFile->temporaryUrl() }}" class="w-full h-full object-cover" />
                        @elseif ($existingImageUrl)
                            <img src="{{ $existingImageUrl }}" class="w-full h-full object-cover" />
                        @else
                            <x-lucide-shopping-bag class="w-10 h-10" />
                        @endif
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            @if ($is_featured)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-accent/10 text-accent uppercase tracking-wider mb-2">
                                    Unggulan
                                </span>
                            @endif
                            <h3 class="text-base font-bold text-primary">{{ $name ?: 'Nama Produk' }}</h3>
                            <p class="text-xs text-slate-500 mt-1 font-mono">slug: {{ $slug ?: 'otomatis' }}</p>
                            @if ($price)
                                <p class="text-sm font-bold text-primary mt-2">Rp {{ number_format($price, 0, ',', '.') }}</p>
                            @endif
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $short_description ?: 'Ulasan singkat produk.' }}
                        </p>

                        <!-- Specifications table preview -->
                        @if (!empty($specs))
                            <div class="border-t border-slate-200 pt-3 space-y-1.5">
                                <span class="text-[10px] font-bold text-primary uppercase">Spesifikasi</span>
                                <div class="grid grid-cols-2 gap-y-1 gap-x-3 text-[11px] text-slate-600 pt-1">
                                    @foreach ($specs as $spec)
                                        @if (!empty($spec['key']))
                                            <span class="font-medium text-slate-400 truncate">{{ $spec['key'] }}:</span>
                                            <span class="text-slate-800 truncate">{{ $spec['value'] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @else
        <!-- Filter Controls -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input 
                    type="text" 
                    wire:model.live="search"
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                    placeholder="Cari produk..."
                />
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select 
                    wire:model.live="filterCategory"
                    class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="filterStatus"
                    class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden relative">
            <div wire:loading wire:target="search, filterCategory, filterStatus" class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
                <div class="flex items-center gap-2 text-slate-500 font-medium text-sm">
                    <x-lucide-loader-2 class="w-5 h-5 animate-spin text-accent" />
                    <span>Memuat...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-primary uppercase tracking-wider">
                            <th class="py-3.5 px-5 w-12 text-center"></th>
                            <th class="py-3.5 px-5 w-20">Foto</th>
                            <th class="py-3.5 px-5">Nama Produk</th>
                            <th class="py-3.5 px-5">Kategori</th>
                            <th class="py-3.5 px-5">Harga</th>
                            <th class="py-3.5 px-5 w-24">Unggulan</th>
                            <th class="py-3.5 px-5 font-semibold text-center w-20">Urutan</th>
                            <th class="py-3.5 px-5 w-24">Status</th>
                            <th class="py-3.5 px-5 w-32 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody 
                        x-data 
                        x-init="
                            new Sortable($el, {
                                handle: '.drag-handle',
                                animation: 150,
                                onEnd: (evt) => {
                                    let items = Array.from($el.children).map((row, index) => {
                                        return { id: row.dataset.id, order: index + 1 }
                                    });
                                    $wire.updateOrder(items);
                                }
                            });
                        "
                        class="divide-y divide-slate-100 text-slate-700"
                    >
                        @forelse ($products as $product)
                            <tr data-id="{{ $product->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="w-12 h-12 bg-slate-100 border border-slate-200 rounded-lg overflow-hidden flex items-center justify-center text-slate-400">
                                        @if ($product->getFirstMediaUrl('image'))
                                            <img src="{{ $product->getFirstMediaUrl('image') }}" class="w-full h-full object-cover" />
                                        @else
                                            <x-lucide-shopping-bag class="w-5 h-5" />
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-primary">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">clicks: {{ $product->whatsapp_click_count }} kali</div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $product->category?->name }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-bold text-primary text-xs">
                                    @if ($product->price)
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleFeatured({{ $product->id }})" class="focus:outline-none">
                                        @if ($product->is_featured)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-accent/10 text-accent">
                                                YA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-400">
                                                TIDAK
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $product->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $product->id }})" class="focus:outline-none">
                                        @if ($product->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-success text-white shadow-sm">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-danger text-white shadow-sm">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="py-3.5 px-5 text-right space-x-1.5">
                                    <button wire:click="edit({{ $product->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $product->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus produk ini?') || event.stopImmediatePropagation()" 
                                        class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 px-5 text-center text-slate-500">
                                    <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                    <p class="text-sm font-medium">Tidak ada data produk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif

        </div>
    @endif
</div>
