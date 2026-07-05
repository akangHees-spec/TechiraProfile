@extends('layouts.app')

@section('breadcrumb')
    <li class="inline-flex items-center">
        <x-lucide-chevron-right class="w-4 h-4 text-slate-400 mx-1" />
        <span class="text-slate-500">Partner</span>
    </li>
@endsection

@section('content')
    <livewire:admin.partner-manager />
@endsection
