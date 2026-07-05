<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Testimonial;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $testimonialId = null;

    // Form fields
    public $name = '';
    public $position = '';
    public $company = '';
    public $message = '';
    public $rating = 5;
    public $is_active = true;
    public $order = 0;

    // Photo
    public $photoFile;
    public $existingPhotoUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_active = !$testimonial->is_active;
        $testimonial->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status testimoni "' . $testimonial->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            Testimonial::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan testimoni berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->testimonialId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $testimonial = Testimonial::findOrFail($id);
        $this->testimonialId = $testimonial->id;
        $this->name = $testimonial->name;
        $this->position = $testimonial->position ?? '';
        $this->company = $testimonial->company ?? '';
        $this->message = $testimonial->message ?? '';
        $this->rating = $testimonial->rating ?? 5;
        $this->is_active = (bool) $testimonial->is_active;
        $this->order = $testimonial->order;
        $this->existingPhotoUrl = $testimonial->getFirstMediaUrl('photo');

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'photoFile' => 'nullable|image|max:1024',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'position' => $this->position,
            'company' => $this->company,
            'message' => $this->message,
            'rating' => $this->rating,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->testimonialId) {
            $testimonial = Testimonial::findOrFail($this->testimonialId);
            $testimonial->update($data);
            $message = 'Testimoni berhasil diperbarui.';
        } else {
            $testimonial = Testimonial::create($data);
            $message = 'Testimoni berhasil ditambahkan.';
        }

        if ($this->photoFile) {
            $testimonial->addMedia($this->photoFile->getRealPath())
                ->usingFileName($this->photoFile->getClientOriginalName())
                ->toMediaCollection('photo');
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
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Testimoni berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->testimonialId = null;
        $this->name = '';
        $this->position = '';
        $this->company = '';
        $this->message = '';
        $this->rating = 5;
        $this->is_active = true;
        $this->order = 0;
        $this->photoFile = null;
        $this->existingPhotoUrl = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Testimonial::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $testimonials = $query->orderBy('order')
                              ->orderBy('id', 'desc')
                              ->paginate(10);

        return view('components.admin.⚡testimonial-manager', [
            'testimonials' => $testimonials
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Testimoni Klien</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola review, rating, dan testimoni kepuasan dari klien.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Testimoni</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $testimonialId ? 'Ubah Testimoni' : 'Tambah Testimoni Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Klien (Name)</label>
                            <input 
                                type="text" 
                                wire:model="name"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('name') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Budi Santoso"
                            />
                            @error('name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Jabatan (Position)</label>
                            <input 
                                type="text" 
                                wire:model="position"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('position') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: CTO, CEO, IT Manager"
                            />
                            @error('position') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Company -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Perusahaan (Company)</label>
                            <input 
                                type="text" 
                                wire:model="company"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('company') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: PT. Maju Bersama"
                            />
                            @error('company') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Rating Bintang</label>
                            <select 
                                wire:model="rating"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                            >
                                <option value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                <option value="3">⭐⭐⭐ (3 Bintang)</option>
                                <option value="2">⭐⭐ (2 Bintang)</option>
                                <option value="1">⭐ (1 Bintang)</option>
                            </select>
                            @error('rating') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Photo File -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Foto Klien</label>
                            <input 
                                type="file" 
                                wire:model="photoFile"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            @error('photoFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Message Text -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Pesan Testimoni (Message)</label>
                            <textarea 
                                wire:model="message"
                                rows="4"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('message') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Isi pesan ulasan dari klien..."
                            ></textarea>
                            @error('message') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
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

                        <!-- Status Toggle -->
                        <div class="flex items-center pt-8">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Testimoni Aktif</span>
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
                
                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 border border-slate-300 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 uppercase text-xs">
                            @if ($photoFile)
                                <img src="{{ $photoFile->temporaryUrl() }}" class="w-full h-full object-cover" />
                            @elseif ($existingPhotoUrl)
                                <img src="{{ $existingPhotoUrl }}" class="w-full h-full object-cover" />
                            @else
                                {{ substr($name ?: 'KL', 0, 2) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-primary truncate">{{ $name ?: 'Nama Klien' }}</h4>
                            <p class="text-[10px] text-slate-500 truncate">
                                {{ $position ? $position : 'Jabatan' }} {{ $company ? 'di ' . $company : '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Stars rating -->
                    <div class="flex items-center gap-0.5 text-[#FBBF24]">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $rating)
                                <x-lucide-star class="w-4 h-4 fill-current text-yellow-500" />
                            @else
                                <x-lucide-star class="w-4 h-4 text-slate-300" />
                            @endif
                        @endfor
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed italic whitespace-pre-line border-t border-slate-200 pt-3">
                        "{{ $message ?: 'Ulasan / testimoni klien akan tampil di sini secara realistis.' }}"
                    </p>
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
                    placeholder="Cari testimoni..."
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
                            <th class="py-3.5 px-5 w-16">Foto</th>
                            <th class="py-3.5 px-5">Nama Klien</th>
                            <th class="py-3.5 px-5">Jabatan / Perusahaan</th>
                            <th class="py-3.5 px-5 w-24">Rating</th>
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
                        @forelse ($testimonials as $testimonial)
                            <tr data-id="{{ $testimonial->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-400 uppercase text-xs">
                                        @if ($testimonial->getFirstMediaUrl('photo'))
                                            <img src="{{ $testimonial->getFirstMediaUrl('photo') }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ substr($testimonial->name, 0, 2) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-primary">{{ $testimonial->name }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-xs">"{{ $testimonial->message }}"</div>
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 text-xs font-medium">
                                    {{ $testimonial->position ?? '-' }} {{ $testimonial->company ? 'at ' . $testimonial->company : '' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-lucide-star class="w-3.5 h-3.5 {{ $i <= $testimonial->rating ? 'fill-current' : 'text-slate-300' }}" />
                                        @endfor
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $testimonial->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $testimonial->id }})" class="focus:outline-none">
                                        @if ($testimonial->is_active)
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
                                    <button wire:click="edit({{ $testimonial->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $testimonial->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus testimoni ini?') || event.stopImmediatePropagation()" 
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
                                    <p class="text-sm font-medium">Tidak ada data testimoni.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($testimonials->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $testimonials->links() }}
                </div>
            @endif

        </div>
    @endif
</div>