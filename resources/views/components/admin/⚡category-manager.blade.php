<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Validation\Rule;

new class extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    // Editor state
    public $isEditing = false;
    public $categoryId = null;

    // Form fields
    public $name = '';
    public $slug = ''; // Optional, generated automatically by spatie sluggable
    public $type = 'product';
    public $icon = 'folder';
    public $description = '';
    public $whatsapp_number = '';
    public $is_active = true;
    public $order = 0;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status kategori "' . $category->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan kategori berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->categoryId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->type = $category->type;
        $this->icon = $category->icon ?: 'folder';
        $this->description = $category->description ?? '';
        $this->whatsapp_number = $category->whatsapp_number ?? '';
        $this->is_active = (bool) $category->is_active;
        $this->order = $category->order;

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['product', 'service'])],
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:30',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon ?: 'folder',
            'description' => $this->description,
            'whatsapp_number' => $this->whatsapp_number,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update($data);
            $message = 'Kategori "' . $this->name . '" berhasil diperbarui.';
        } else {
            $category = Category::create($data);
            $message = 'Kategori "' . $this->name . '" berhasil ditambahkan.';
        }

        $this->isEditing = false;
        $this->resetForm();

        session()->flash('toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->type = 'product';
        $this->icon = 'folder';
        $this->description = '';
        $this->whatsapp_number = '';
        $this->is_active = true;
        $this->order = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Category::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $categories = $query->orderBy('order')
                            ->orderBy('id', 'desc')
                            ->paginate(10);

        return view('components.admin.⚡category-manager', [
            'categories' => $categories
        ]);
    }
};
?>

<div>
    <!-- Toast Notification Widget -->
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
                @if (session('toast')['type'] === 'success')
                    <x-lucide-check-circle class="w-6 h-6 text-success" />
                @else
                    <x-lucide-alert-circle class="w-6 h-6 text-danger" />
                @endif
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Kelola Kategori</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelompokkan produk dan layanan jasa perusahaan.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Kategori</span>
            </button>
        @endif
    </div>

    <!-- Edit/Create Section (2-Column Layout) -->
    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $categoryId ? 'Ubah Kategori: ' . $name : 'Tambah Kategori Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Kategori</label>
                            <input 
                                type="text" 
                                wire:model.live="name"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('name') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Cloud Infrastructure"
                            />
                            @error('name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Tipe Kategori</label>
                            <select 
                                wire:model="type"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('type') border-danger focus:ring-danger focus:border-danger @enderror"
                            >
                                <option value="product">Produk (Product)</option>
                                <option value="service">Jasa Layanan (Service)</option>
                            </select>
                            @error('type') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Icon Key -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Kode Ikon Lucide</label>
                            <input 
                                type="text" 
                                wire:model.live="icon"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('icon') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: server, code, cloud, database"
                            />
                            <p class="text-[10px] text-slate-500 mt-1">Nama ikon Lucide lowercase, misal: smartphone, laptop, shield.</p>
                            @error('icon') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- WhatsApp Override -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">WhatsApp Override (Opsional)</label>
                            <input 
                                type="text" 
                                wire:model="whatsapp_number"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('whatsapp_number') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: 08123456789"
                            />
                            <p class="text-[10px] text-slate-500 mt-1">Kosongkan untuk menggunakan nomor WhatsApp default dari Pengaturan.</p>
                            @error('whatsapp_number') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Urutan (Order)</label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('order') border-danger focus:ring-danger focus:border-danger @enderror"
                            />
                            @error('order') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Aktif Toggle -->
                        <div class="flex items-center pt-8">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Kategori Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Deskripsi Kategori</label>
                        <textarea 
                            wire:model="description"
                            rows="4"
                            class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('description') border-danger focus:ring-danger focus:border-danger @enderror"
                            placeholder="Deskripsikan kategori ini secara singkat..."
                        ></textarea>
                        @error('description') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Action Buttons -->
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
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 h-fit">
                <h2 class="text-xs font-semibold text-primary uppercase tracking-wider mb-4">Live Preview</h2>
                
                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-accent text-white rounded-lg">
                            <x-dynamic-component :component="'lucide-' . ($icon ?: 'folder')" class="w-6 h-6" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider {{ $type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ $type === 'product' ? 'Produk' : 'Jasa' }}
                            </span>
                            <h3 class="text-base font-bold text-primary mt-2 truncate">{{ $name ?: 'Nama Kategori' }}</h3>
                            <p class="text-xs text-slate-500 mt-1 font-mono truncate">slug: {{ $slug ?: 'otomatis-terbuat' }}</p>
                            <p class="text-sm text-slate-600 mt-3 whitespace-pre-line leading-relaxed">
                                {{ $description ?: 'Masukan deskripsi kategori untuk melihat preview teks di sini.' }}
                            </p>
                            
                            @if ($whatsapp_number)
                                <div class="mt-4 pt-4 border-t border-slate-200 flex items-center gap-2 text-xs text-success font-semibold">
                                    <x-lucide-phone-call class="w-4 h-4" />
                                    <span>Override WA: {{ $whatsapp_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-xs text-slate-400 space-y-2">
                    <div class="flex justify-between">
                        <span>Urutan Tampil:</span>
                        <span class="font-semibold text-slate-700">{{ $order }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status Aktif:</span>
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
        <!-- Filter and Search controls -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search Box -->
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input 
                    type="text" 
                    wire:model.live="search"
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                    placeholder="Cari kategori..."
                />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Type Filter -->
                <select 
                    wire:model.live="filterType"
                    class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                >
                    <option value="">Semua Tipe</option>
                    <option value="product">Tipe: Produk</option>
                    <option value="service">Tipe: Jasa</option>
                </select>

                <!-- Status Filter -->
                <select 
                    wire:model.live="filterStatus"
                    class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Status: Aktif</option>
                    <option value="0">Status: Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Data Table Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden relative">
            
            <!-- Loading Indicator Overlay -->
            <div wire:loading wire:target="search, filterType, filterStatus" class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
                <div class="flex items-center gap-2 text-slate-500 font-medium text-sm">
                    <x-lucide-loader-2 class="w-5 h-5 animate-spin text-accent" />
                    <span>Memuat data...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-primary uppercase tracking-wider">
                            <th class="py-3.5 px-5 w-12 text-center"></th>
                            <th class="py-3.5 px-5 w-16">Ikon</th>
                            <th class="py-3.5 px-5">Nama Kategori</th>
                            <th class="py-3.5 px-5">Slug</th>
                            <th class="py-3.5 px-5">Tipe</th>
                            <th class="py-3.5 px-5 font-semibold text-center w-20">Urutan</th>
                            <th class="py-3.5 px-5 w-28">Status</th>
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
                        @forelse ($categories as $index => $category)
                            <tr data-id="{{ $category->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="p-2 bg-slate-100 rounded-lg text-primary w-fit">
                                        <x-dynamic-component :component="'lucide-' . ($category->icon ?: 'folder')" class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-primary">{{ $category->name }}</div>
                                    @if ($category->description)
                                        <div class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-sm">{{ $category->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-500">{{ $category->slug }}</td>
                                <td class="py-3.5 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider {{ $category->type === 'product' ? 'bg-blue-50 text-blue-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $category->type === 'product' ? 'Produk' : 'Jasa' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $category->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button 
                                        wire:click="toggleActive({{ $category->id }})"
                                        class="focus:outline-none"
                                        title="Ubah status"
                                    >
                                        @if ($category->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-success text-white transition-all shadow-sm">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-danger text-white transition-all shadow-sm">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="py-3.5 px-5 text-right space-x-1.5">
                                    <button 
                                        wire:click="edit({{ $category->id }})"
                                        class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none"
                                        title="Ubah Kategori"
                                    >
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $category->id }})"
                                        onclick="confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua produk/jasa terkait di dalamnya akan ikut terhapus.') || event.stopImmediatePropagation()"
                                        class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none"
                                        title="Hapus Kategori"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 px-5 text-center text-slate-500">
                                    <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                    <p class="text-sm font-medium">Tidak ada kategori ditemukan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            @if ($categories->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>
    @endif
</div>