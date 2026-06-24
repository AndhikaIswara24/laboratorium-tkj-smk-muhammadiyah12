@extends('layouts.app')
@section('title','Dashboard User')
@section('content')

<!-- Header Section -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        <i class="fas fa-home text-blue-600"></i> Dashboard
    </h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }} — lihat aset tersedia dan informasi laboratorium.</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Aset Tersedia Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-blue-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Aset Tersedia</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">1,240</p>
                <p class="text-xs text-gray-500 mt-1">Siap digunakan</p>
            </div>
            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-cube text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Informasi Laboratorium Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Status Lab</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 flex items-center gap-2">
                    <span class="h-3 w-3 bg-green-500 rounded-full animate-pulse"></span>
                    Operasional
                </p>
                <p class="text-xs text-gray-500 mt-1">Jam kerja: 07:00 - 16:00</p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Featured Sections -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Useful Links -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-link text-purple-500 mr-2"></i>Menu Penting
        </h2>
        <div class="space-y-3">
            <a href="{{ route('assets.index') }}" class="block p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors text-blue-700 font-medium text-sm">
                <i class="fas fa-cube mr-2"></i>Lihat Semua Aset
            </a>
            <a href="{{ route('profile.edit') }}" class="block p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors text-purple-700 font-medium text-sm">
                <i class="fas fa-user-edit mr-2"></i>Profil Saya
            </a>
            <a href="{{ route('dashboard') }}" class="block p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors text-green-700 font-medium text-sm">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Lab Information -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>Informasi Laboratorium TKJ
        </h2>
        <div class="space-y-4 text-sm">
            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <i class="fas fa-clock text-blue-600 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Jam Operasional</p>
                    <p class="text-gray-600">Senin - Jumat: 07:00 - 16:00</p>
                    <p class="text-gray-600">Sabtu: 07:00 - 12:00</p>
                </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <i class="fas fa-map-marker-alt text-blue-600 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Lokasi</p>
                    <p class="text-gray-600">Ruang Lab TKJ, Lantai 2</p>
                    <p class="text-gray-600">SMK Muhammadiyah 12</p>
                </div>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <i class="fas fa-user-tie text-blue-600 text-lg flex-shrink-0 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Penanggung Jawab</p>
                    <p class="text-gray-600">Drs. Ahmad Wijaya</p>
                    <p class="text-gray-600">Teknisi: Budi Santoso</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Equipment -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-boxes text-orange-500 mr-2"></i>Peralatan Laboratorium Terpopuler
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-desktop text-blue-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Komputer PC</p>
                    <p class="text-xs text-gray-600">12 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-laptop text-green-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Laptop</p>
                    <p class="text-xs text-gray-600">5 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-print text-purple-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Printer</p>
                    <p class="text-xs text-gray-600">3 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-wifi text-red-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Router WiFi</p>
                    <p class="text-xs text-gray-600">2 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-server text-yellow-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Server</p>
                    <p class="text-xs text-gray-600">1 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-network-wired text-indigo-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900">Network Tools</p>
                    <p class="text-xs text-gray-600">8 unit tersedia</p>
                    <p class="text-xs text-green-600 font-semibold mt-2">Kondisi: Baik ✓</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg shadow-md p-6 border border-yellow-200">
    <h2 class="text-lg font-bold text-yellow-900 mb-4">
        <i class="fas fa-bullhorn text-orange-600 mr-2"></i>Pengumuman
    </h2>
    <ul class="space-y-3 text-sm">
        <li class="flex items-start gap-3">
            <span class="inline-block w-2 h-2 bg-orange-600 rounded-full mt-2 flex-shrink-0"></span>
            <span class="text-yellow-900"><strong>24 Juni 2026:</strong> Sistem inventaris lab telah diupdate dengan fitur tracking real-time.</span>
        </li>
        <li class="flex items-start gap-3">
            <span class="inline-block w-2 h-2 bg-orange-600 rounded-full mt-2 flex-shrink-0"></span>
            <span class="text-yellow-900"><strong>20 Juni 2026:</strong> Maintenance rutin laboratory semua peralatan dilakukan.</span>
        </li>
        <li class="flex items-start gap-3">
            <span class="inline-block w-2 h-2 bg-orange-600 rounded-full mt-2 flex-shrink-0"></span>
            <span class="text-yellow-900"><strong>15 Juni 2026:</strong> Penambahan 5 unit komputer baru di Lab TKJ.</span>
        </li>
    </ul>
</div>

@endsection
