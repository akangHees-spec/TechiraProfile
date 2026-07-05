<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Faq;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $faqId = null;

    // Form fields
    public $question = '';
    public $answer = '';
    public $is_active = true;
    public $order = 0;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status FAQ berhasil diperbarui.'
        ]);
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            Faq::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan FAQ berhasil diperbarui.'
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->faqId = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $faq = Faq::findOrFail($id);
        $this->faqId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->is_active = (bool) $faq->is_active;
        $this->order = $faq->order;

        $this->isEditing = true;
    }

    public function save()
    {
        $rules = [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ];

        $this->validate($rules);

        $data = [
            'question' => $this->question,
            'answer' => $this->answer,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->faqId) {
            $faq = Faq::findOrFail($this->faqId);
            $faq->update($data);
            $message = 'FAQ berhasil diperbarui.';
        } else {
            $faq = Faq::create($data);
            $message = 'FAQ berhasil ditambahkan.';
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
        $faq = Faq::findOrFail($id);
        $faq->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'FAQ berhasil dihapus.'
        ]);
    }

    public function cancel()
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->faqId = null;
        $this->question = '';
        $this->answer = '';
        $this->is_active = true;
        $this->order = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Faq::query();

        if (!empty($this->search)) {
            $query->where('question', 'like', '%' . $this->search . '%')
                  ->orWhere('answer', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $faqs = $query->orderBy('order')
                      ->orderBy('id', 'desc')
                      ->paginate(10);

        return view('components.admin.⚡faq-manager', [
            'faqs' => $faqs
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Kelola FAQ</h1>
            <p class="text-sm text-slate-500 mt-1">Mengelola daftar pertanyaan yang sering diajukan beserta jawabannya.</p>
        </div>
        
        @if (!$isEditing)
            <button 
                wire:click="create"
                class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white font-medium text-sm rounded-lg transition-colors shadow-sm focus:outline-none"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Tambah FAQ</span>
            </button>
        @endif
    </div>

    @if ($isEditing)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Form Area -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-primary mb-6">
                    {{ $faqId ? 'Ubah FAQ' : 'Tambah FAQ Baru' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        
                        <!-- Question -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Pertanyaan (Question)</label>
                            <input 
                                type="text" 
                                wire:model="question"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('question') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Tulis pertanyaan..."
                            />
                            @error('question') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Answer -->
                        <div>
                            <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Jawaban (Answer)</label>
                            <textarea 
                                wire:model="answer"
                                rows="6"
                                class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors @error('answer') border-danger focus:ring-danger focus:border-danger @enderror"
                                placeholder="Tulis jawaban lengkap dari pertanyaan tersebut..."
                            ></textarea>
                            @error('answer') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                    <span class="ml-3 text-sm font-semibold text-primary">FAQ Aktif</span>
                                </label>
                            </div>
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
                
                <div class="p-5 border border-slate-200 rounded-lg bg-slate-50 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <h4 class="text-sm font-bold text-primary leading-snug">
                            Q: {{ $question ?: 'Teks pertanyaan FAQ' }}
                        </h4>
                        <x-lucide-chevron-down class="w-4 h-4 text-slate-400 flex-shrink-0" />
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3 whitespace-pre-line">
                        {{ $answer ?: 'Jawaban untuk pertanyaan ini akan ditampilkan di sini.' }}
                    </p>
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
                    placeholder="Cari FAQ..."
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
                            <th class="py-3.5 px-5">Pertanyaan (Question)</th>
                            <th class="py-3.5 px-5">Jawaban (Answer Preview)</th>
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
                        @forelse ($faqs as $faq)
                            <tr data-id="{{ $faq->id }}" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3.5 px-5 text-center">
                                    <div class="drag-handle cursor-grab text-slate-400 hover:text-slate-600 active:cursor-grabbing p-1 flex justify-center">
                                        <x-lucide-grip-vertical class="w-4 h-4" />
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-primary">
                                    {{ $faq->question }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-500 text-xs line-clamp-1 max-w-sm">
                                    {{ $faq->answer }}
                                </td>
                                <td class="py-3.5 px-5 text-center font-bold text-xs text-slate-600">
                                    {{ $faq->order }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <button wire:click="toggleActive({{ $faq->id }})" class="focus:outline-none">
                                        @if ($faq->is_active)
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
                                    <button wire:click="edit({{ $faq->id }})" class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        wire:click="delete({{ $faq->id }})" 
                                        onclick="confirm('Apakah Anda yakin ingin menghapus FAQ ini?') || event.stopImmediatePropagation()" 
                                        class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg"
                                    >
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-5 text-center text-slate-500">
                                    <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                    <p class="text-sm font-medium">Tidak ada data FAQ.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($faqs->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $faqs->links() }}
                </div>
            @endif

        </div>
    @endif
</div>