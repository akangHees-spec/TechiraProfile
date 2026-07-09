<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Post;
use Illuminate\Support\Str;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $postId = null;

    // Form fields
    public $title = '';
    public $slug = '';
    public $content = '';
    public $is_published = false;
    public $published_at = '';

    // Cover Photo
    public $coverFile;
    public $existingCoverUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatedTitle($value)
    {
        if (!$this->postId) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedIsPublished($value)
    {
        if ($value) {
            if (!$this->published_at) {
                $this->published_at = now()->format('Y-m-d\TH:i');
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function togglePublished($id)
    {
        $post = Post::findOrFail($id);
        $post->is_published = !$post->is_published;
        if ($post->is_published && !$post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status artikel "' . $post->title . '" berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->postId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $post = Post::findOrFail($id);
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->content = $post->content;
        $this->is_published = (bool) $post->is_published;
        $this->published_at = $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '';
        $this->existingCoverUrl = $post->getFirstMediaUrl('cover');

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . ($this->postId ?? 'NULL'),
            'content' => 'nullable|string',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'coverFile' => 'nullable|image|max:2048', // 2MB max
        ];

        $this->validate($rules);

        $publishedAtVal = null;
        if ($this->is_published) {
            $publishedAtVal = $this->published_at ? \Carbon\Carbon::parse($this->published_at) : now();
        } elseif ($this->published_at) {
            $publishedAtVal = \Carbon\Carbon::parse($this->published_at);
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'is_published' => $this->is_published,
            'published_at' => $publishedAtVal,
        ];

        if ($this->postId) {
            $post = Post::findOrFail($this->postId);
            $post->update($data);
            $message = 'Artikel berhasil diperbarui.';
        } else {
            $post = Post::create($data);
            $message = 'Artikel baru berhasil dibuat.';
        }

        if ($this->coverFile) {
            $post->addMedia($this->coverFile)->toMediaCollection('cover');
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
        $post = Post::findOrFail($id);
        $post->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Artikel berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->is_published = false;
        $this->published_at = '';
        $this->coverFile = null;
        $this->existingCoverUrl = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Post::query();

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_published', $this->filterStatus);
        }

        $posts = $query->orderBy('id', 'desc')->paginate(10);

        return view('components.admin.⚡post-manager', [
            'posts' => $posts
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Blog / Artikel</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola konten artikel, berita, dan blog perusahaan.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah Artikel</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Form Area (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $postId ? 'Ubah Artikel' : 'Tambah Artikel Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Judul Artikel</label>
                            <input 
                                type="text" 
                                wire:model.live="title"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('title') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Contoh: 5 Tren Teknologi di Tahun 2026"
                            />
                            @error('title') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Slug URL (Otomatis)</label>
                            <input 
                                type="text" 
                                wire:model="slug"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('slug') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="tren-teknologi-2026"
                            />
                            @error('slug') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rich Text Content -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider">Isi Konten</label>
                            <div
                                x-data="{ 
                                    content: @entangle('content'),
                                    init() {
                                        const quill = new Quill(this.$refs.editor, {
                                            theme: 'snow',
                                            modules: {
                                                toolbar: [
                                                    [{ 'header': [1, 2, 3, false] }],
                                                    ['bold', 'italic', 'underline', 'strike'],
                                                    ['blockquote', 'code-block'],
                                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                    ['link', 'image'],
                                                    ['clean']
                                                ]
                                            }
                                        });
                                        quill.root.innerHTML = this.content || '';
                                        quill.on('text-change', () => {
                                            this.content = quill.root.innerHTML;
                                        });
                                        this.$watch('content', value => {
                                            if (value !== quill.root.innerHTML) {
                                                quill.root.innerHTML = value || '';
                                            }
                                        });
                                    }
                                }"
                                wire:ignore
                                class="w-full"
                            >
                                <style>
                                    .ql-toolbar.ql-snow {
                                        border: none !important;
                                        border-bottom: 1px solid #e2e8f0 !important;
                                        background-color: #f8fafc;
                                        padding: 8px 12px !important;
                                    }
                                    .ql-container.ql-snow {
                                        border: none !important;
                                        font-family: inherit;
                                        font-size: 14px;
                                    }
                                    .ql-editor {
                                        min-height: 280px;
                                        font-family: inherit;
                                    }
                                </style>
                                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
                                    <div x-ref="editor" class="text-slate-800 focus:outline-none"></div>
                                </div>
                            </div>
                            @error('content') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="button"
                            wire:click="cancel"
                            class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium text-xs rounded-lg transition-colors focus:outline-none"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-xs rounded-lg transition-colors shadow-sm focus:outline-none"
                        >
                            Simpan Artikel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Sidebar Form Area (1 col) -->
            <div class="space-y-6">
                <!-- Cover Image Card -->
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-primary mb-4">Foto Sampul (Cover)</h3>
                    
                    <div class="space-y-4">
                        <!-- Preview -->
                        <div class="w-full aspect-[16/9] rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center relative group">
                            @if ($coverFile)
                                <img src="{{ $coverFile->temporaryUrl() }}" class="w-full h-full object-cover" />
                            @elseif ($existingCoverUrl)
                                <img src="{{ $existingCoverUrl }}" class="w-full h-full object-cover" />
                            @else
                                <div class="text-center p-4">
                                    <x-lucide-image class="w-8 h-8 text-slate-400 mx-auto mb-2" />
                                    <span class="text-xs text-slate-400">Belum ada foto sampul</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload field -->
                        <div>
                            <input 
                                type="file" 
                                wire:model="coverFile"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary hover:file:bg-slate-200"
                            />
                            @error('coverFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Publication Card -->
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-primary mb-4">Pengaturan Publikasi</h3>
                    
                    <div class="space-y-4">
                        <!-- Is Published Toggle -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-primary uppercase tracking-wider">Terbitkan Langsung</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="is_published" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                            </label>
                        </div>

                        <!-- Published At datetimepicker -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Tanggal Terbit</label>
                            <input 
                                type="datetime-local"
                                wire:model="published_at"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                            />
                            @error('published_at') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>
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
                    wire:model.live.debounce.300ms="search"
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
                    placeholder="Cari judul artikel..."
                />
            </div>

            <select 
                wire:model.live="filterStatus"
                class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent transition-colors"
            >
                <option value="">Semua Status</option>
                <option value="1">Published</option>
                <option value="0">Draft</option>
            </select>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden relative">
            <div wire:loading wire:target="search, filterStatus" class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
                <div class="flex items-center gap-2 text-slate-500 font-medium text-sm">
                    <x-lucide-loader-2 class="w-4 h-4 animate-spin text-accent" />
                    <span>Memuat data...</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-semibold text-primary uppercase tracking-wider">Artikel</th>
                            <th class="px-6 py-4 text-xs font-semibold text-primary uppercase tracking-wider">Tanggal Terbit</th>
                            <th class="px-6 py-4 text-xs font-semibold text-primary uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-primary uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($posts as $post)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-16 aspect-video rounded bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                            @if ($post->getFirstMediaUrl('cover'))
                                                <img src="{{ $post->getFirstMediaUrl('cover') }}" class="w-full h-full object-cover" />
                                            @else
                                                <x-lucide-image class="w-4 h-4 text-slate-400" />
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-primary line-clamp-1">{{ $post->title }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $post->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $post->published_at ? $post->published_at->translatedFormat('d F Y, H:i') : 'Draft' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        wire:click="togglePublished({{ $post->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all focus:outline-none {{ $post->is_published ? 'bg-success/10 text-success hover:bg-success/20' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $post->is_published ? 'bg-success' : 'bg-slate-400' }}"></span>
                                        <span>{{ $post->is_published ? 'Published' : 'Draft' }}</span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            wire:click="edit({{ $post->id }})"
                                            class="p-1.5 border border-slate-200 hover:border-accent text-slate-400 hover:text-accent rounded-lg transition-colors focus:outline-none"
                                            title="Ubah"
                                        >
                                            <x-lucide-edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            wire:confirm="Apakah Anda yakin ingin menghapus artikel ini?"
                                            wire:click="delete({{ $post->id }})"
                                            class="p-1.5 border border-slate-200 hover:border-danger text-slate-400 hover:text-danger rounded-lg transition-colors focus:outline-none"
                                            title="Hapus"
                                        >
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <x-lucide-newspaper class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                                    <p class="text-sm font-semibold text-slate-400">Belum ada artikel</p>
                                    <p class="text-xs text-slate-400 mt-1">Tekan tombol "Tambah Artikel" untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($posts->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
