<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContactMessage;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRead = '';

    // Message viewer detail modal
    public $viewingMessage = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRead()
    {
        $this->resetPage();
    }

    public function toggleRead($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->is_read = !$msg->is_read;
        $msg->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status pesan berhasil diperbarui.'
        ]);
    }

    public function showMessage($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->is_read = true;
        $msg->save();

        $this->viewingMessage = $msg;
    }

    public function closeMessage()
    {
        $this->viewingMessage = null;
    }

    public function delete($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->delete();

        if ($this->viewingMessage && $this->viewingMessage->id == $id) {
            $this->viewingMessage = null;
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Pesan berhasil dihapus.'
        ]);
    }

    public function render()
    {
        $query = ContactMessage::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
        }

        if ($this->filterRead !== '') {
            $query->where('is_read', $this->filterRead);
        }

        $messages = $query->orderBy('created_at', 'desc')
                          ->paginate(10);

        return view('components.admin.⚡contact-message-manager', [
            'messages' => $messages
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
            <h1 class="text-2xl font-bold tracking-tight text-primary">Pesan Masuk</h1>
            <p class="text-sm text-slate-500 mt-1">Membaca dan mengelola pesan / formulir kontak yang dikirim oleh pengunjung web.</p>
        </div>
    </div>

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
                placeholder="Cari pengirim, subjek, atau isi pesan..."
            />
        </div>

        <select 
            wire:model.live="filterRead"
            class="px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
        >
            <option value="">Semua Status</option>
            <option value="0">Belum Dibaca</option>
            <option value="1">Sudah Dibaca</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden relative">
        <div wire:loading wire:target="search, filterRead" class="absolute inset-0 bg-white/60 z-10 flex items-center justify-center">
            <div class="flex items-center gap-2 text-slate-500 font-medium text-sm">
                <x-lucide-loader-2 class="w-5 h-5 animate-spin text-accent" />
                <span>Memuat...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-primary uppercase tracking-wider">
                        <th class="py-3.5 px-5 w-24">Status</th>
                        <th class="py-3.5 px-5">Pengirim</th>
                        <th class="py-3.5 px-5">Subjek / Preview</th>
                        <th class="py-3.5 px-5">Tanggal Masuk</th>
                        <th class="py-3.5 px-5 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($messages as $msg)
                        <tr class="hover:bg-slate-50/50 transition-colors {{ !$msg->is_read ? 'bg-blue-50/20 font-medium' : '' }}">
                            <td class="py-3.5 px-5">
                                <button wire:click="toggleRead({{ $msg->id }})" class="focus:outline-none">
                                    @if (!$msg->is_read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-accent/15 text-accent">
                                            BARU
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-400">
                                            DIBACA
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="text-primary font-semibold">{{ $msg->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $msg->email }}</div>
                                @if ($msg->phone)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $msg->phone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="text-primary font-semibold truncate max-w-xs">{{ $msg->subject ?: '(Tanpa Subjek)' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 truncate max-w-sm">{{ $msg->message }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-slate-500 text-xs">
                                {{ $msg->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
                                <button 
                                    wire:click="showMessage({{ $msg->id }})" 
                                    class="p-1.5 text-accent hover:text-accent/80 hover:bg-slate-100 rounded-lg"
                                    title="Baca Pesan"
                                >
                                    <x-lucide-eye class="w-4 h-4" />
                                </button>
                                <button 
                                    wire:click="delete({{ $msg->id }})" 
                                    onclick="confirm('Apakah Anda yakin ingin menghapus pesan ini?') || event.stopImmediatePropagation()" 
                                    class="p-1.5 text-danger hover:text-danger/80 hover:bg-slate-100 rounded-lg"
                                    title="Hapus"
                                >
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-5 text-center text-slate-500">
                                <x-lucide-inbox class="w-10 h-10 text-slate-300 mx-auto mb-3" />
                                <p class="text-sm font-medium">Kotak masuk kosong.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($messages->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

    <!-- Message Detail Modal (Slide-up/Fade Panel) -->
    @if ($viewingMessage)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/40">
            <div 
                x-data
                @click.outside="$wire.closeMessage()"
                class="bg-white rounded-lg border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden"
            >
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-primary text-base">Detail Pesan Masuk</h3>
                    <button wire:click="closeMessage" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Sender Details -->
                    <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-lg border border-slate-100">
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase">Pengirim</span>
                            <span class="font-bold text-primary block text-sm mt-1">{{ $viewingMessage->name }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase">Tanggal</span>
                            <span class="text-slate-700 block text-sm mt-1">{{ $viewingMessage->created_at->format('d F Y, H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase">Email</span>
                            <a href="mailto:{{ $viewingMessage->email }}" class="text-accent font-semibold block text-sm mt-1 hover:underline">{{ $viewingMessage->email }}</a>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-400 block uppercase">Telepon (Phone)</span>
                            <span class="text-slate-700 block text-sm mt-1">{{ $viewingMessage->phone ?: '-' }}</span>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <span class="font-semibold text-slate-400 text-xs uppercase block">Subjek</span>
                        <h4 class="text-sm font-bold text-primary mt-1 border-b border-slate-100 pb-2">
                            {{ $viewingMessage->subject ?: '(Tanpa Subjek)' }}
                        </h4>
                    </div>

                    <!-- Message Body -->
                    <div>
                        <span class="font-semibold text-slate-400 text-xs uppercase block">Isi Pesan</span>
                        <div class="text-sm text-slate-700 leading-relaxed bg-slate-50/50 p-4 border border-slate-100 rounded-lg mt-2 whitespace-pre-line h-48 overflow-y-auto">
                            {{ $viewingMessage->message }}
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <button 
                        wire:click="delete({{ $viewingMessage->id }})" 
                        onclick="confirm('Apakah Anda yakin ingin menghapus pesan ini?') || event.stopImmediatePropagation()"
                        class="px-4 py-2 bg-danger hover:bg-danger/90 text-white font-medium text-xs rounded-lg transition-colors focus:outline-none"
                    >
                        Hapus Pesan
                    </button>
                    <button 
                        wire:click="closeMessage" 
                        class="px-4 py-2 border border-slate-200 hover:bg-slate-100 text-slate-600 font-medium text-xs rounded-lg transition-colors focus:outline-none"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>