<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TeamMember;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $memberId = null;

    // Form fields
    public $name = '';
    public $position = '';
    public $bio = '';
    public $is_active = true;
    public $order = 0;

    // Social Links inputs
    public $social_facebook = '';
    public $social_instagram = '';
    public $social_linkedin = '';
    public $social_github = '';

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
        $member = TeamMember::findOrFail($id);
        $member->is_active = !$member->is_active;
        $member->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status anggota tim "' . $member->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            TeamMember::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan anggota tim berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->memberId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $member = TeamMember::findOrFail($id);
        $this->memberId = $member->id;
        $this->name = $member->name;
        $this->position = $member->position;
        $this->bio = $member->bio ?? '';
        $this->is_active = (bool) $member->is_active;
        $this->order = $member->order;
        $this->existingPhotoUrl = $member->getFirstMediaUrl('photo');

        // Fill social links
        $socials = $member->social_links ?? [];
        $this->social_facebook = $socials['facebook'] ?? '';
        $this->social_instagram = $socials['instagram'] ?? '';
        $this->social_linkedin = $socials['linkedin'] ?? '';
        $this->social_github = $socials['github'] ?? '';

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_github' => 'nullable|url|max:255',
            'photoFile' => 'nullable|image|max:1536',
        ];

        $this->validate($rules);

        $socialLinks = [
            'facebook' => $this->social_facebook,
            'instagram' => $this->social_instagram,
            'linkedin' => $this->social_linkedin,
            'github' => $this->social_github,
        ];

        // Remove empty links
        $socialLinks = array_filter($socialLinks);

        $data = [
            'name' => $this->name,
            'position' => $this->position,
            'bio' => $this->bio,
            'social_links' => $socialLinks,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->memberId) {
            $member = TeamMember::findOrFail($this->memberId);
            $member->update($data);
            $message = 'Anggota tim berhasil diperbarui.';
        } else {
            $member = TeamMember::create($data);
            $message = 'Anggota tim berhasil ditambahkan.';
        }

        if ($this->photoFile) {
            $member->addMedia($this->photoFile->getRealPath())
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
        $member = TeamMember::findOrFail($id);
        $member->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Anggota tim berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->memberId = null;
        $this->name = '';
        $this->position = '';
        $this->bio = '';
        $this->is_active = true;
        $this->order = 0;
        $this->social_facebook = '';
        $this->social_instagram = '';
        $this->social_linkedin = '';
        $this->social_github = '';
        $this->photoFile = null;
        $this->existingPhotoUrl = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = TeamMember::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('position', 'like', '%' . $this->search . '%')
                  ->orWhere('bio', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $members = $query->orderBy('order')
                         ->orderBy('id', 'desc')
                         ->paginate(10);

        return view('components.admin.⚡team-member-manager', [
            'members' => $members
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Anggota Tim</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola profil profesional tim / staff perusahaan.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Anggota</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $memberId ? 'Ubah Profil Tim' : 'Tambah Anggota Tim Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Lengkap (Name)</label>
                            <input 
                                type="text" 
                                wire:model="name"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('name') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: Amanda Putri"
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
                                placeholder="Contoh: Chief Technology Officer"
                            />
                            @error('position') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Photo File -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Foto Profil</label>
                            <input 
                                type="file" 
                                wire:model="photoFile"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            <p class="text-[10px] text-slate-500 mt-1">Rekomendasi rasio portrait/persegi (Maks: 1.5MB).</p>
                            @error('photoFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bio -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Biografi Singkat (Bio)</label>
                            <textarea 
                                wire:model="bio"
                                rows="3"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('bio') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Tulis biografi singkat tentang tim ini..."
                            ></textarea>
                            @error('bio') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Social Media URL Header -->
                        <div class="md:col-span-2 pt-2 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Tautan Media Sosial</h3>
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Facebook URL</label>
                            <input 
                                type="text" 
                                wire:model="social_facebook"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                                placeholder="https://facebook.com/username"
                            />
                            @error('social_facebook') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Instagram URL</label>
                            <input 
                                type="text" 
                                wire:model="social_instagram"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                                placeholder="https://instagram.com/username"
                            />
                            @error('social_instagram') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">LinkedIn URL</label>
                            <input 
                                type="text" 
                                wire:model="social_linkedin"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                                placeholder="https://linkedin.com/in/username"
                            />
                            @error('social_linkedin') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- GitHub -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">GitHub URL</label>
                            <input 
                                type="text" 
                                wire:model="social_github"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                                placeholder="https://github.com/username"
                            />
                            @error('social_github') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
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

                        <!-- Status Aktif -->
                        <div class="flex items-center pt-8">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success"></div>
                                <span class="ml-3 text-sm font-semibold text-primary">Tampilkan di Web</span>
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
                
                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50 text-center space-y-4">
                    <div class="w-28 h-28 rounded-full overflow-hidden bg-slate-200 border border-slate-300 mx-auto flex items-center justify-center font-bold text-slate-500 uppercase text-2xl">
                        @if ($photoFile)
                            <img src="{{ $photoFile->temporaryUrl() }}" class="w-full h-full object-cover" />
                        @elseif ($existingPhotoUrl)
                            <img src="{{ $existingPhotoUrl }}" class="w-full h-full object-cover" />
                        @else
                            {{ substr($name ?: 'TM', 0, 2) }}
                        @endif
                    </div>
                    
                    <div>
                        <h4 class="text-base font-bold text-primary">{{ $name ?: 'Nama Anggota' }}</h4>
                        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $position ?: 'Jabatan' }}</p>
                    </div>

                    @if ($bio)
                        <p class="text-xs text-slate-600 leading-relaxed max-w-xs mx-auto">
                            {{ $bio }}
                        </p>
                    @endif

                    <!-- Social Icons Preview -->
                    <div class="flex items-center justify-center gap-3 pt-2">
                        @if ($social_facebook)
                            <x-lucide-facebook class="w-4 h-4 text-slate-400 hover:text-accent" />
                        @endif
                        @if ($social_instagram)
                            <x-lucide-instagram class="w-4 h-4 text-slate-400 hover:text-accent" />
                        @endif
                        @if ($social_linkedin)
                            <x-lucide-linkedin class="w-4 h-4 text-slate-400 hover:text-accent" />
                        @endif
                        @if ($social_github)
                            <x-lucide-github class="w-4 h-4 text-slate-400 hover:text-accent" />
                        @endif
                        @if (!$social_facebook && !$social_instagram && !$social_linkedin && !$social_github)
                            <span class="text-[10px] text-slate-400">Belum ada sosmed ditambahkan</span>
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
                    placeholder="Cari anggota tim..."
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
                            <th class="py-3.5 px-5">Nama Lengkap</th>
                            <th class="py-3.5 px-5">Jabatan</th>
                            <th class="py-3.5 px-5">Sosmed</th>
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
                        @forelse ($members as $member)
                            <tr data-id="{{ $member->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-400 uppercase text-xs">
                                        @if ($member->getFirstMediaUrl('photo'))
                                            <img src="{{ $member->getFirstMediaUrl('photo') }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ substr($member->name, 0, 2) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="font-semibold text-primary">{{ $member->name }}</div>
                                    @if ($member->bio)
                                        <div class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-xs">{{ $member->bio }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 font-medium text-slate-600">
                                    {{ $member->position }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-1.5 text-slate-400">
                                        @if (isset($member->social_links['facebook']))
                                            <x-lucide-facebook class="w-3.5 h-3.5" />
                                        @endif
                                        @if (isset($member->social_links['instagram']))
                                            <x-lucide-instagram class="w-3.5 h-3.5" />
                                        @endif
                                        @if (isset($member->social_links['linkedin']))
                                            <x-lucide-linkedin class="w-3.5 h-3.5" />
                                        @endif
                                        @if (isset($member->social_links['github']))
                                            <x-lucide-github class="w-3.5 h-3.5" />
                                        @endif
                                        @if (empty($member->social_links))
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $member->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $member->id }})" class="focus:outline-none">
                                        @if ($member->is_active)
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
                                    <button wire:click="edit({{ $member->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $member->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus profil anggota ini?') || event.stopImmediatePropagation()" 
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
                                    <p class="text-sm font-medium">Tidak ada data anggota tim.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $members->links() }}
                </div>
            @endif

        </div>
    @endif
</div>