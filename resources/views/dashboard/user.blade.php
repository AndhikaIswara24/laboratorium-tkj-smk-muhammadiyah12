@extends('layouts.app')
@section('title', 'Dashboard User')
@section('content')

<!-- Header Section -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        <i class="fas fa-home text-blue-600"></i> Dashboard
    </h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }} — Lihat ketersediaan aset laboratorium dan informasi laboratorium.</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Aset Tersedia Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-blue-600 dark:bg-slate-900 dark:border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Aset Layak Pakai</p>
                <p class="text-3xl font-black text-gray-900 mt-2 dark:text-white">{{ $distribKondisi['Layak'] }} Unit</p>
                <p class="text-xs text-slate-400 mt-1">Siap digunakan untuk kegiatan KBM</p>
            </div>
            <div class="h-12 w-12 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center">
                <i class="fas fa-cube text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Informasi Laboratorium Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-600 dark:bg-slate-900 dark:border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Status Operasional Lab</p>
                <p class="text-2xl font-black text-emerald-600 mt-2 flex items-center gap-2">
                    <span class="h-3 w-3 bg-green-500 rounded-full animate-pulse"></span>
                    Aktif
                </p>
                <p class="text-xs text-slate-400 mt-1">Jam KBM: 07:00 - 16:00 WIB</p>
            </div>
            <div class="h-12 w-12 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Featured Sections -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Menu Penting -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
            <i class="fas fa-link text-purple-500 mr-2"></i>Akses Pintas
        </h2>
        <div class="space-y-3">
            <a href="{{ route('profile.edit') }}" class="block p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 font-semibold text-sm">
                <i class="fas fa-user-edit mr-2"></i>Ubah Profil Saya
            </a>
        </div>
    </div>

    <!-- Lab Information -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>Informasi Laboratorium TKJ
        </h2>
        <div class="space-y-4 text-sm">
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-950/20 rounded-lg">
                <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">Jam Kerja Lab</p>
                    <p class="text-gray-600 dark:text-gray-400">Senin - Jumat: 07:00 - 16:00 WIB</p>
                </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-950/20 rounded-lg">
                <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">Lokasi</p>
                    <p class="text-gray-600 dark:text-gray-400">Gedung Lab TKJ Lantai 2, SMK Muhammadiyah 12</p>
                </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-950/20 rounded-lg">
                <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">Kepala Lab & Staf</p>
                    <p class="text-gray-600 dark:text-gray-400">Kepala Lab: Alvian Fiqra Ramadhan S.Kom.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Equipment (Dynamic counts based on name filters) -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
    <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
        <i class="fas fa-boxes text-orange-500 mr-2"></i>Peralatan Laboratorium Tersedia
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $pcCount = \App\Models\Asset::where('nama_brg', 'like', '%komputer%')->orWhere('nama_brg', 'like', '%pc%')->count();
            $laptopCount = \App\Models\Asset::where('nama_brg', 'like', '%laptop%')->count();
            $printerCount = \App\Models\Asset::where('nama_brg', 'like', '%printer%')->count();
            $routerCount = \App\Models\Asset::where('nama_brg', 'like', '%router%')->count();
            $serverCount = \App\Models\Asset::where('nama_brg', 'like', '%server%')->count();
            $switchCount = \App\Models\Asset::where('nama_brg', 'like', '%switch%')->orWhere('nama_brg', 'like', '%hub%')->count();
        @endphp

        {{-- PC Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-desktop text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Komputer PC</p>
                    <p class="text-xs text-gray-500">{{ $pcCount }} unit terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Laptop Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-laptop text-green-600 dark:text-green-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Laptop</p>
                    <p class="text-xs text-gray-500">{{ $laptopCount }} unit terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Printer Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-purple-100 dark:bg-purple-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-print text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Printer</p>
                    <p class="text-xs text-gray-500">{{ $printerCount }} unit terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Router Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-red-100 dark:bg-red-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-wifi text-red-600 dark:text-red-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Router WiFi</p>
                    <p class="text-xs text-gray-500">{{ $routerCount }} unit terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Server Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-server text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Server</p>
                    <p class="text-xs text-gray-500">{{ $serverCount }} unit terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Switch/Hub Card --}}
        <div class="p-4 border border-gray-200 dark:border-slate-800 rounded-lg">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-network-wired text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Switch / Hub</p>
                    <p class="text-xs text-gray-500">{{ $switchCount }} unit terdaftar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-slate-900 dark:to-slate-950 rounded-lg shadow-md p-6 border border-yellow-200 dark:border-slate-800">
    <h2 class="text-lg font-bold text-yellow-900 dark:text-amber-500 mb-4">
        <i class="fas fa-bullhorn text-orange-600 dark:text-orange-400 mr-2"></i>Informasi & Pengumuman
    </h2>
    <ul class="space-y-3 text-sm">
        <li class="flex items-start gap-3">
            <span class="inline-block w-2 h-2 bg-orange-600 rounded-full mt-2 flex-shrink-0"></span>
            <span class="text-slate-700 dark:text-slate-300"><strong>Informasi:</strong> Seluruh data kelayakan diestimasi secara cerdas menggunakan algoritma klasifikasi Naive Bayes.</span>
        </li>
        <li class="flex items-start gap-3">
            <span class="inline-block w-2 h-2 bg-orange-600 rounded-full mt-2 flex-shrink-0"></span>
            <span class="text-slate-700 dark:text-slate-300"><strong>Himbauan:</strong> Harap menjaga kebersihan laboratorium dan mematikan peralatan setelah jam KBM selesai.</span>
        </li>
    </ul>
</div>

@endsection
