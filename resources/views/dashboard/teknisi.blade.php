@extends('layouts.app')
@section('title','Dashboard Teknisi')
@section('content')

<!-- Header Section -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        <i class="fas fa-tools text-green-600"></i> Dashboard Teknisi
    </h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }} — kelola aset laboratorium Anda di sini.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Aset Dipelihara Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-orange-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Aset Dipelihara</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">156</p>
                <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
            </div>
            <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-tools text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Kondisi Baik Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Aset Kondisi Baik</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">892</p>
                <p class="text-xs text-green-600 mt-1">71.9% dari total</p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Perlu Perbaikan Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-yellow-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Perlu Perbaikan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">48</p>
                <p class="text-xs text-yellow-600 mt-1">3.9% dari total</p>
            </div>
            <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Quick Actions -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Aksi Cepat
        </h2>
        <div class="space-y-3">
            <a href="{{ route('assets.index') }}" class="block p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors text-blue-700 font-medium text-sm">
                <i class="fas fa-cube mr-2"></i>Lihat Semua Aset
            </a>
            <a href="{{ route('kondisi.index') }}" class="block p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors text-green-700 font-medium text-sm">
                <i class="fas fa-stethoscope mr-2"></i>Kondisi Fisik
            </a>
            <a href="{{ route('pemeliharaan.index') }}" class="block p-3 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors text-orange-700 font-medium text-sm">
                <i class="fas fa-tools mr-2"></i>Catat Pemeliharaan
            </a>
            <a href="{{ route('efisiensi.index') }}" class="block p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors text-purple-700 font-medium text-sm">
                <i class="fas fa-tachometer-alt mr-2"></i>Efisiensi
            </a>
        </div>
    </div>

    <!-- Tasks Today -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-tasks text-blue-500 mr-2"></i>Tugas Hari Ini
        </h2>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <input type="checkbox" class="mt-1 rounded">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Periksa Kondisi Printer Lab 1</p>
                    <p class="text-xs text-gray-600">Maintenance rutin bulanan</p>
                    <p class="text-xs text-gray-500 mt-1">Prioritas: <span class="text-orange-600 font-semibold">Tinggi</span></p>
                </div>
                <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded font-medium">Pending</span>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <input type="checkbox" class="mt-1 rounded">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Cleaning Server Room</p>
                    <p class="text-xs text-gray-600">Pembersihan ruang server</p>
                    <p class="text-xs text-gray-500 mt-1">Prioritas: <span class="text-yellow-600 font-semibold">Sedang</span></p>
                </div>
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded font-medium">In Progress</span>
            </div>

            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <input type="checkbox" checked class="mt-1 rounded">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 line-through text-gray-500">Update Software Semua Komputer</p>
                    <p class="text-xs text-gray-600">Windows Update batch</p>
                    <p class="text-xs text-gray-500 mt-1">Prioritas: <span class="text-blue-600 font-semibold">Rendah</span></p>
                </div>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">Selesai</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Kondisi Aset -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-pie text-green-500 mr-2"></i>Status Kondisi Aset
        </h2>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 font-medium">Kondisi Baik</span>
                    <span class="text-green-600 font-bold">71.9%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-green-500 h-3 rounded-full" style="width: 71.9%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 font-medium">Perlu Perbaikan</span>
                    <span class="text-yellow-600 font-bold">3.9%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-yellow-500 h-3 rounded-full" style="width: 3.9%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 font-medium">Rusak</span>
                    <span class="text-red-600 font-bold">24.2%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-red-500 h-3 rounded-full" style="width: 24.2%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Maintenance -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-history text-blue-500 mr-2"></i>Pemeliharaan Terakhir
        </h2>
        <div class="space-y-3">
            <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Printer HP LaserJet</p>
                        <p class="text-xs text-gray-600">Cleaning & cartridge replacement</p>
                    </div>
                    <span class="text-xs text-gray-500">2 hari lalu</span>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Monitor LCD 21"</p>
                        <p class="text-xs text-gray-600">Screen cleaning & cable check</p>
                    </div>
                    <span class="text-xs text-gray-500">5 hari lalu</span>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-orange-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">CPU Server</p>
                        <p class="text-xs text-gray-600">Temperature check & cooling system</p>
                    </div>
                    <span class="text-xs text-gray-500">1 minggu lalu</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tips & Notifications -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 border border-blue-200">
    <h2 class="text-lg font-bold text-blue-900 mb-4">
        <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Tips & Pengingat
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div class="flex gap-3">
            <i class="fas fa-check-circle text-green-600 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-medium text-blue-900">Maintenance Terjadwal</p>
                <p class="text-blue-700 text-xs">Lakukan maintenance bulanan sesuai jadwal</p>
            </div>
        </div>
        <div class="flex gap-3">
            <i class="fas fa-exclamation-circle text-orange-600 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-medium text-blue-900">Aset Rusak</p>
                <p class="text-blue-700 text-xs">Ada 48 aset yang perlu perbaikan segera</p>
            </div>
        </div>
    </div>
</div>

@endsection
