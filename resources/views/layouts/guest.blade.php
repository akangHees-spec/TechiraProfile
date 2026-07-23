<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F8FAFC] text-[#0F172A] min-h-screen sm:h-screen sm:overflow-hidden flex items-center justify-center p-0 sm:p-6">
    
    <!-- Outer Card Wrapper -->
    <!-- On mobile (under md): full width/height, scrollable right column if needed. On desktop (md+): locked width/height grid. -->
    <div class="w-full h-screen sm:h-auto sm:max-h-[600px] max-w-4xl bg-white sm:rounded-xl shadow-sm sm:border border-slate-200 overflow-hidden grid grid-cols-1 md:grid-cols-2">
        
        <!-- Left Column: Techira Sidebar Corporate Banner (Hidden on mobile to keep focus on login form) -->
        <div class="hidden md:flex bg-[#0F172A] p-8 sm:p-10 flex-col justify-between text-white relative">
            <!-- Logo Header -->
            <div class="relative z-10 flex items-center gap-2">
                <img src="{{ asset('images/images.jpg') }}" alt="Techira Logo" class="h-8 w-auto object-contain rounded" />
                <span class="font-bold text-base tracking-wider">TECHIRA</span>
            </div>

            <!-- Illustration -->
            <div class="flex flex-col items-center justify-center my-4">
                <img 
                    src="{{ asset('images/techira_login_worker.png') }}" 
                    alt="Office Worker Illustration" 
                    class="w-full max-w-[200px] h-auto object-contain rounded-lg"
                />
            </div>

            <!-- Slogan & Footer -->
            <div class="space-y-1">
                <h3 class="text-sm font-bold tracking-tight">Work, Explore & Repeat</h3>
                <p class="text-[10px] text-slate-400">Portal Keamanan & Layanan IT Techira Nusantara.</p>
            </div>
        </div>

        <!-- Right Column: Login Form Content Slot -->
        <!-- On mobile it will take full screen, allowing smooth form interaction -->
        <div class="p-8 sm:p-10 flex flex-col justify-center bg-white h-full sm:h-auto overflow-y-auto">
            <!-- Mobile Header Logo (Only visible on mobile) -->
            <div class="flex items-center gap-2 mb-6 md:hidden">
                <img src="{{ asset('images/images.jpg') }}" alt="Techira Logo" class="h-8 w-auto object-contain rounded" />
                <span class="font-extrabold text-base tracking-wider text-[#0F172A]">TECHIRA</span>
            </div>
            
            {{ $slot }}
        </div>

    </div>

</body>

</html>
