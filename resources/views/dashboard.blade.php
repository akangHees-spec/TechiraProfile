@extends('layouts.app')

@section('breadcrumb')
    <!-- Dashboard breadcrumb is root/empty by default -->
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-primary">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}. Berikut adalah ikhtisar performa bisnis hari ini.</p>
    </div>

    <livewire:admin.dashboard-stats />
@endsection
