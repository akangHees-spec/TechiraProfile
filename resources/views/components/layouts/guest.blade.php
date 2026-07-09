@php
    $favicon = \App\Models\Setting::where('key', 'favicon')->value('value') ?? '/favicon.ico';
    $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Techira Nusantara';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? $companyName }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $favicon }}">

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="antialiased font-sans bg-white text-slate-800">
    
    {{ $slot }}

    @livewireScripts
</body>
</html>
