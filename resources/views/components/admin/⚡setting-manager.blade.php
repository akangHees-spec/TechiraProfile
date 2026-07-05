<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;

new class extends Component
{
    use WithFileUploads;

    public $activeTab = 'general'; // general, contact, social

    // Form fields mapped to setting keys
    public $company_name = '';
    public $address = '';
    public $google_maps_embed = '';

    public $email = '';
    public $phone = '';
    public $whatsapp_number = '';
    public $whatsapp_message_template = '';

    public $social_facebook = '';
    public $social_instagram = '';
    public $social_linkedin = '';
    public $youtube_url = '';

    // Files
    public $logoFile;
    public $faviconFile;
    public $existingLogoUrl = null;
    public $existingFaviconUrl = null;

    public function mount()
    {
        $this->loadSettings();
    }

    private function loadSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');

        $this->company_name = $settings['company_name'] ?? '';
        $this->address = $settings['address'] ?? '';
        $this->google_maps_embed = $settings['google_maps_embed'] ?? '';

        $this->email = $settings['email'] ?? '';
        $this->phone = $settings['phone'] ?? '';
        $this->whatsapp_number = $settings['whatsapp_number'] ?? '';
        $this->whatsapp_message_template = $settings['whatsapp_message_template'] ?? '';

        $this->social_facebook = $settings['social_facebook'] ?? '';
        $this->social_instagram = $settings['social_instagram'] ?? '';
        $this->social_linkedin = $settings['social_linkedin'] ?? '';
        $this->youtube_url = $settings['youtube_url'] ?? '';

        $this->existingLogoUrl = $settings['logo'] ?? null;
        $this->existingFaviconUrl = $settings['favicon'] ?? null;
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function save()
    {
        $rules = [
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'google_maps_embed' => 'nullable|string',
            
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:30',
            'whatsapp_number' => 'required|string|max:30',
            'whatsapp_message_template' => 'required|string',

            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',

            'logoFile' => 'nullable|image|max:1024',
            'faviconFile' => 'nullable|image|max:512',
        ];

        $this->validate($rules);

        $updates = [
            'company_name' => $this->company_name,
            'address' => $this->address,
            'google_maps_embed' => $this->google_maps_embed,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp_number' => $this->whatsapp_number,
            'whatsapp_message_template' => $this->whatsapp_message_template,
            'social_facebook' => $this->social_facebook,
            'social_instagram' => $this->social_instagram,
            'social_linkedin' => $this->social_linkedin,
            'youtube_url' => $this->youtube_url,
        ];

        foreach ($updates as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Upload Logo
        if ($this->logoFile) {
            $logoPath = $this->logoFile->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'logo'], ['value' => '/storage/' . $logoPath]);
        }

        // Upload Favicon
        if ($this->faviconFile) {
            $favPath = $this->faviconFile->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'favicon'], ['value' => '/storage/' . $favPath]);
        }

        $this->loadSettings();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Pengaturan sistem berhasil diperbarui.'
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

    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-primary">Pengaturan Website</h1>
        <p class="text-sm text-slate-500 mt-1">Mengelola identitas perusahaan, info kontak, dan tautan sosial media secara terpusat.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Navigation Menu Tabs (Left) -->
        <div class="lg:col-span-1 space-y-1">
            <button 
                wire:click="changeTab('general')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors text-left {{ $activeTab === 'general' ? 'bg-white border-l-4 border-accent text-accent shadow-sm' : 'text-slate-500 hover:bg-white hover:text-primary' }}"
            >
                <x-lucide-info class="w-4 h-4" />
                <span>Info Umum</span>
            </button>
            <button 
                wire:click="changeTab('contact')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors text-left {{ $activeTab === 'contact' ? 'bg-white border-l-4 border-accent text-accent shadow-sm' : 'text-slate-500 hover:bg-white hover:text-primary' }}"
            >
                <x-lucide-phone class="w-4 h-4" />
                <span>Kontak & WhatsApp</span>
            </button>
            <button 
                wire:click="changeTab('social')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors text-left {{ $activeTab === 'social' ? 'bg-white border-l-4 border-accent text-accent shadow-sm' : 'text-slate-500 hover:bg-white hover:text-primary' }}"
            >
                <x-lucide-share-2 class="w-4 h-4" />
                <span>Media Sosial</span>
            </button>
        </div>

        <!-- Form Editor (Right) -->
        <div class="lg:col-span-3 bg-white rounded-lg border border-slate-200 shadow-sm p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                
                <!-- Tab: General Info -->
                <div x-show="$wire.activeTab === 'general'" class="space-y-6">
                        <h2 class="text-base font-bold text-primary border-b border-slate-100 pb-3">Identitas Perusahaan</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Name -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nama Perusahaan</label>
                                <input 
                                    type="text" 
                                    wire:model="company_name"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"
                                />
                                @error('company_name') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo File -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Logo Utama</label>
                                <input type="file" wire:model="logoFile" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary" />
                                <div class="mt-3 w-32 h-14 border border-slate-200 rounded-lg p-2 bg-slate-50 flex items-center justify-center overflow-hidden">
                                    @if ($logoFile)
                                        <img src="{{ $logoFile->temporaryUrl() }}" class="max-w-full max-h-full object-contain" />
                                    @elseif ($existingLogoUrl)
                                        <img src="{{ $existingLogoUrl }}" class="max-w-full max-h-full object-contain" />
                                    @else
                                        <span class="text-[10px] text-slate-400">Tidak ada logo</span>
                                    @endif
                                </div>
                                @error('logoFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Favicon File -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Favicon</label>
                                <input type="file" wire:model="faviconFile" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-primary" />
                                <div class="mt-3 w-10 h-10 border border-slate-200 rounded-lg p-2 bg-slate-50 flex items-center justify-center overflow-hidden">
                                    @if ($faviconFile)
                                        <img src="{{ $faviconFile->temporaryUrl() }}" class="max-w-full max-h-full object-contain" />
                                    @elseif ($existingFaviconUrl)
                                        <img src="{{ $existingFaviconUrl }}" class="max-w-full max-h-full object-contain" />
                                    @else
                                        <span class="text-[10px] text-slate-400">Icon</span>
                                    @endif
                                </div>
                                @error('faviconFile') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Alamat Fisik</label>
                                <textarea 
                                    wire:model="address"
                                    rows="3"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                ></textarea>
                                @error('address') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Google Maps iframe -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Google Maps Embed (Iframe HTML)</label>
                                <textarea 
                                    wire:model="google_maps_embed"
                                    rows="4"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm font-mono bg-white text-primary focus:outline-none focus:border-accent"
                                    placeholder="Tulis tag <iframe ...></iframe> dari Google Maps Share"
                                ></textarea>
                                @error('google_maps_embed') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                </div>

                <!-- Tab: Contact & WhatsApp -->
                <div x-show="$wire.activeTab === 'contact'" class="space-y-6">
                        <h2 class="text-base font-bold text-primary border-b border-slate-100 pb-3">Informasi Kontak & Pesan WhatsApp</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Email Perusahaan</label>
                                <input 
                                    type="email" 
                                    wire:model="email"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                />
                                @error('email') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nomor Telepon Kantor</label>
                                <input 
                                    type="text" 
                                    wire:model="phone"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                />
                                @error('phone') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- WhatsApp Number -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Nomor WhatsApp Penerima</label>
                                <input 
                                    type="text" 
                                    wire:model="whatsapp_number"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                    placeholder="Contoh: 08123456789"
                                />
                                @error('whatsapp_number') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- WhatsApp template -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Template Pesan WhatsApp</label>
                                <textarea 
                                    wire:model="whatsapp_message_template"
                                    rows="3"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
                                    placeholder="Halo Techira, saya tertarik dengan {name}. Link: {url}"
                                ></textarea>
                                <p class="text-[10px] text-slate-500 mt-1">Gunakan placeholder `{name}` untuk menyisipkan nama produk/jasa, dan `{url}` untuk menyisipkan URL halaman produk/jasa terkait.</p>
                                @error('whatsapp_message_template') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                </div>

                <!-- Tab: Social Media Links -->
                <div x-show="$wire.activeTab === 'social'" class="space-y-6">
                        <h2 class="text-base font-bold text-primary border-b border-slate-100 pb-3">Tautan Media Sosial</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Facebook -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">Facebook Page URL</label>
                                <input 
                                    type="text" 
                                    wire:model="social_facebook"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none focus:border-accent"
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
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                                    placeholder="https://instagram.com/username"
                                />
                                @error('social_instagram') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- LinkedIn -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">LinkedIn Company URL</label>
                                <input 
                                    type="text" 
                                    wire:model="social_linkedin"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                                    placeholder="https://linkedin.com/company/username"
                                />
                                @error('social_linkedin') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- YouTube -->
                            <div>
                                <label class="block text-xs font-semibold text-primary uppercase tracking-wider mb-2">YouTube Channel URL</label>
                                <input 
                                    type="text" 
                                    wire:model="youtube_url"
                                    class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm bg-white text-primary focus:outline-none"
                                    placeholder="https://youtube.com/c/username"
                                />
                                @error('youtube_url') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                </div>

                <!-- Validation Error Summary -->
                @if ($errors->any())
                    <div class="p-4 bg-danger/10 border border-danger/20 rounded-lg text-xs text-danger flex items-start gap-2.5 mt-4">
                        <x-lucide-alert-triangle class="w-4 h-4 flex-shrink-0 mt-0.5 text-danger" />
                        <div>
                            <span class="font-bold">Gagal Menyimpan:</span> Ada kesalahan pengisian data pada form. Silakan cek kembali tab **Info Umum**, **Kontak & WhatsApp**, atau **Media Sosial** yang ditandai merah.
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-accent hover:bg-accent/90 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm focus:outline-none flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>