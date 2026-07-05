<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Partner;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $partnerId = null;

    // Form fields
    public $name = '';
    public $website_url = '';
    public $is_active = true;
    public $order = 0;

    // Logo
    public $logoFile;
    public $existingLogoUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->is_active = !$partner->is_active;
        $partner->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status partner "' . $partner->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            Partner::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan partner berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->partnerId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $partner = Partner::findOrFail($id);
        $this->partnerId = $partner->id;
        $this->name = $partner->name;
        $this->website_url = $partner->website_url ?? '';
        $this->is_active = (bool) $partner->is_active;
        $this->order = $partner->order;
        $this->existingLogoUrl = $partner->getFirstMediaUrl('logo');

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'logoFile' => 'nullable|image|max:1024',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'website_url' => $this->website_url,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->partnerId) {
            $partner = Partner::findOrFail($this->partnerId);
            $partner->update($data);
            $message = 'Partner berhasil diperbarui.';
        } else {
            $partner = Partner::create($data);
            $message = 'Partner berhasil ditambahkan.';
        }

        if ($this->logoFile) {
            $partner->addMedia($this->logoFile->getRealPath())
                ->usingFileName($this->logoFile->getClientOriginalName())
                ->toMediaCollection('logo');
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
        $partner = Partner::findOrFail($id);
        $partner->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Partner berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->partnerId = null;
        $this->name = '';
        $this->website_url = '';
        $this->is_active = true;
        $this->order = 0;
        $this->logoFile = null;
        $this->existingLogoUrl = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Partner::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('website_url', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $partners = $query->orderBy('order')
                          ->orderBy('id', 'desc')
                          ->paginate(10);

        return view('components.admin.⚡partner-manager', [
            'partners' => $partners
        ]);
    }
};
?>

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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Partner Bisnis</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola logo partner kerja sama, client portfolio, atau vendor perusahaan.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Partner</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $partnerId ? 'Ubah Partner' : 'Tambah Partner Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Partner (Name)</label>
                            <input 
                                type="text" 
                                wire:model="name"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('name') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Amazon Web Services"
                            />
                            @error('name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Website URL -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Tautan Website (Website URL)</label>
                            <input 
                                type="text" 
                                wire:model="website_url"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('website_url') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="https://example.com"
                            />
                            @error('website_url') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Logo File -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Logo Partner</label>
                            <input 
                                type="file" 
                                wire:model="logoFile"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            <p class="text-[10px] text-slate-500 mt-1">Gunakan logo transparan (.PNG) atau SVG demi estetika landing page (Maks: 1MB).</p>
                            @error('logoFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Urutan (Order)</label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                            />
                        </div>

                        <!-- Status Aktif Toggle -->
                        <div class="flex items-center pt-8">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Partner Aktif</span>
                            </label>
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
                
                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50 text-center space-y-3">
                    <div class="w-32 h-16 bg-white border border-slate-200 rounded-lg mx-auto flex items-center justify-center p-3 overflow-hidden">
                        @if ($logoFile)
                            <img src="{{ $logoFile->temporaryUrl() }}" class="max-w-full max-h-full object-contain filter grayscale hover:grayscale-0 transition-all duration-300" />
                        @elseif ($existingLogoUrl)
                            <img src="{{ $existingLogoUrl }}" class="max-w-full max-h-full object-contain filter grayscale hover:grayscale-0 transition-all duration-300" />
                        @else
                            <span class="text-xs font-bold text-slate-400 uppercase">{{ $name ?: 'LOGO' }}</span>
                        @endif
                    </div>
                    
                    <h4 class="text-sm font-bold text-primary">{{ $name ?: 'Nama Partner' }}</h4>
                    
                    @if ($website_url)
                        <a href="{{ $website_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-accent font-semibold hover:underline">
                            <x-lucide-external-link class="w-3.5 h-3.5" />
                            <span>Kunjungi Website</span>
                        </a>
                    @endif
                </div>

                <div class="text-xs text-slate-400 space-y-2">
                    <div class="flex justify-between">
                        <span>Urutan:</span>
                        <span class="font-semibold text-slate-700">{{ $order }}</span>
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
                    placeholder="Cari partner..."
                />
            </div>

            <select 
                wire:model.live="filterStatus"
                class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
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
                            <th class="py-3.5 px-5 w-24">Logo</th>
                            <th class="py-3.5 px-5">Nama Partner</th>
                            <th class="py-3.5 px-5">Website URL</th>
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
                        @forelse ($partners as $partner)
                            <tr data-id="{{ $partner->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="w-16 h-10 bg-white border border-slate-200 rounded-lg p-1.5 flex items-center justify-center overflow-hidden">
                                        @if ($partner->getFirstMediaUrl('logo'))
                                            <img src="{{ $partner->getFirstMediaUrl('logo') }}" class="max-w-full max-h-full object-contain filter grayscale" />
                                        @else
                                            <span class="text-[9px] font-bold text-slate-400">{{ substr($partner->name, 0, 3) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-primary">
                                    {{ $partner->name }}
                                </td>
                                <td class="py-3.5 px-5 text-xs font-mono text-slate-500">
                                    @if ($partner->website_url)
                                        <a href="{{ $partner->website_url }}" target="_blank" class="text-accent hover:underline flex items-center gap-1">
                                            <x-lucide-external-link class="w-3.5 h-3.5" />
                                            <span>{{ $partner->website_url }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $partner->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $partner->id }})" class="focus:outline-none">
                                        @if ($partner->is_active)
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
                                    <button wire:click="edit({{ $partner->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $partner->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus partner ini?') || event.stopImmediatePropagation()" 
                                        class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-5 text-center text-slate-500">
                                    <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                    <p class="text-sm font-medium">Tidak ada data partner.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($partners->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $partners->links() }}
                </div>
            @endif

        </div>
    @endif
</div>