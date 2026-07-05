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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Slider Hero</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola gambar latar, judul promosi, dan CTA di halaman utama.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Slider</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $sliderId ? 'Ubah Slider' : 'Tambah Slider Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Judul Utama (Title)</label>
                            <input 
                                type="text" 
                                wire:model="title"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('title') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Transformasi Digital Bisnis Anda"
                            />
                            @error('title') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subtitle -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Sub-Judul (Subtitle)</label>
                            <textarea 
                                wire:model="subtitle"
                                rows="2"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('subtitle') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Deskripsi pendek pendukung judul utama..."
                            ></textarea>
                            @error('subtitle') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Button Text -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Teks Tombol (Button Text)</label>
                            <input 
                                type="text" 
                                wire:model="button_text"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('button_text') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Hubungi Kami"
                            />
                            @error('button_text') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Button Link -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Tautan Tombol (Button Link)</label>
                            <input 
                                type="text" 
                                wire:model="button_link"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('button_link') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: #contact atau /services"
                            />
                            @error('button_link') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Gambar Background</label>
                            <input 
                                type="file" 
                                wire:model="imageFile"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            <p class="text-[10px] text-slate-500 mt-1">Format: JPG, PNG, WebP (Maks: 2MB). Rekomendasi rasio landscape 16:9.</p>
                            @error('imageFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Urutan (Order)</label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                            />
                            @error('order') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Aktif Toggle -->
                        <div class="flex items-center pt-8">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Slider Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="save">Simpan Slider</span>
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
                
                <!-- Graphic Hero Banner Placeholder -->
                <div class="relative w-full aspect-[16/9] bg-primary text-white rounded-lg overflow-hidden border border-slate-800 p-6 flex flex-col justify-center">
                    
                    @if ($imageFile)
                        <img src="{{ $imageFile->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover opacity-35 z-0" />
                    @elseif ($existingImageUrl)
                        <img src="{{ $existingImageUrl }}" class="absolute inset-0 w-full h-full object-cover opacity-35 z-0" />
                    @endif

                    <div class="relative z-10 space-y-2 max-w-[85%]">
                        <h3 class="text-xs font-bold md:text-sm tracking-tight text-white leading-tight font-sans line-clamp-2">
                            {{ $title ?: 'Judul Utama Banner' }}
                        </h3>
                        <p class="text-[9px] md:text-xs text-slate-300 leading-normal line-clamp-3">
                            {{ $subtitle ?: 'Teks pendukung / deskripsi singkat slider akan tampil di sini untuk memikat pengunjung.' }}
                        </p>
                        
                        @if ($button_text)
                            <div class="pt-2">
                                <span class="inline-block px-3 py-1 bg-accent text-white text-[9px] font-semibold rounded hover:bg-accent/90 transition-colors">
                                    {{ $button_text }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-xs text-slate-400 space-y-2">
                    <div class="flex justify-between">
                        <span>Urutan:</span>
                        <span class="font-semibold text-slate-700">{{ $order }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        @if ($is_active)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold bg-danger/10 text-danger">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @else
        <!-- Filter Control -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input 
                    type="text" 
                    wire:model.live="search"
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                    placeholder="Cari slider..."
                />
            </div>

            <select 
                wire:model.live="filterStatus"
                class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
            >
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden relative">
            
            <div wire:loading wire:target="search, filterStatus" class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
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
                            <th class="py-3.5 px-5 w-24">Gambar</th>
                            <th class="py-3.5 px-5">Informasi Slider</th>
                            <th class="py-3.5 px-5">Tautan Tombol</th>
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
                        @forelse ($sliders as $slider)
                            <tr data-id="{{ $slider->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="w-16 h-10 bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                        @if ($slider->getFirstMediaUrl('image'))
                                            <img src="{{ $slider->getFirstMediaUrl('image') }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <x-lucide-image class="w-5 h-5" />
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-primary">{{ $slider->title }}</div>
                                    @if ($slider->subtitle)
                                        <div class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-md">{{ $slider->subtitle }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600">
                                    @if ($slider->button_text)
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <x-lucide-external-link class="w-3.5 h-3.5 text-accent" />
                                            <span>{{ $slider->button_text }} ({{ $slider->button_link }})</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $slider->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $slider->id }})" class="focus:outline-none">
                                        @if ($slider->is_active)
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
                                    <button wire:click="edit({{ $slider->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $slider->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus slider ini?') || event.stopImmediatePropagation()" 
                                        class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-5 text-center text-slate-500">
                                    <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                    <p class="text-sm font-medium">Tidak ada data slider.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sliders->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $sliders->links() }}
                </div>
            @endif

        </div>
    @endif
</div>
