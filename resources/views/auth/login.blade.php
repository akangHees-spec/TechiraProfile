<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-[#0F172A] tracking-tight">Login</h2>
        <p class="text-xs text-slate-500 mt-1">Masukkan email dan password Anda untuk masuk ke dashboard admin.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Kantor</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-lucide-mail class="w-4 h-4" />
                </div>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    placeholder="nama@techira.com"
                    class="block w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-slate-200 rounded-lg text-xs text-[#0F172A] placeholder-slate-400 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-150"
                />
            </div>
            @error('email')
                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-semibold text-accent hover:underline" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-lucide-lock class="w-4 h-4" />
                </div>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="block w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-slate-200 rounded-lg text-xs text-[#0F172A] placeholder-slate-400 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-all duration-150"
                />
            </div>
            @error('password')
                <span class="text-[10px] text-danger mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    name="remember"
                    class="w-4 h-4 rounded border-slate-200 text-accent focus:ring-accent bg-[#F8FAFC]"
                />
                <span class="ms-2 text-xs font-medium text-slate-500 select-none">Ingat saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white font-bold rounded-lg text-xs tracking-wider uppercase transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"
            >
                Masuk
            </button>
        </div>
    </form>
</x-guest-layout>
