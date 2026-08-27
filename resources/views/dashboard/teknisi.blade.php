@extends('layouts.app')
@section('title', 'Dashboard Teknisi')
@section('content')

<!-- Header Section -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
        <i class="fas fa-tools text-green-600"></i> Dashboard Teknisi
    </h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }} — Kelola observasi fisik dan pemeliharaan laboratorium Anda.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Aset Dipelihara Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-orange-600 dark:bg-slate-900 dark:border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Aset Dipelihara (PM)</p>
                <p class="text-3xl font-black text-gray-900 mt-2 dark:text-white">{{ $countPemeliharaan }}</p>
                <p class="text-xs text-gray-400 mt-1">Total riwayat tindakan pemeliharaan</p>
            </div>
            <div class="h-12 w-12 bg-orange-100 dark:bg-orange-500/10 rounded-lg flex items-center justify-center">
                <i class="fas fa-tools text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Kondisi Layak Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-600 dark:bg-slate-900 dark:border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Aset Kondisi Layak</p>
                <p class="text-3xl font-black text-green-600 mt-2">{{ $distribKondisi['Layak'] }}</p>
                <p class="text-xs text-green-600 mt-1">
                    {{ $totalAssets > 0 ? round(($distribKondisi['Layak'] / $totalAssets) * 100, 1) : 0 }}% dari total aset
                </p>
            </div>
            <div class="h-12 w-12 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Butuh Perbaikan Card -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-yellow-600 dark:bg-slate-900 dark:border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Butuh Perbaikan / Tidak Layak</p>
                <p class="text-3xl font-black text-yellow-600 mt-2">
                    {{ $distribKondisi['Perlu Servis'] + $distribKondisi['Tidak Layak'] }}
                </p>
                <p class="text-xs text-yellow-600 mt-1">
                    {{ $totalAssets > 0 ? round((($distribKondisi['Perlu Servis'] + $distribKondisi['Tidak Layak']) / $totalAssets) * 100, 1) : 0 }}% dari total aset
                </p>
            </div>
            <div class="h-12 w-12 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Quick Actions -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Aksi Cepat Teknisi
        </h2>
        <div class="space-y-3">
            <a href="{{ route('assets.index') }}" class="block p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 font-semibold text-sm">
                <i class="fas fa-cube mr-2"></i>Lihat & Kelola Aset
            </a>
            <a href="{{ route('kondisi.index') }}" class="block p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors text-green-700 dark:bg-green-500/10 dark:text-green-400 font-semibold text-sm">
                <i class="fas fa-stethoscope mr-2"></i>Kondisi Fisik
            </a>
            <a href="{{ route('pemeliharaan.index') }}" class="block p-3 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 font-semibold text-sm">
                <i class="fas fa-tools mr-2"></i>Catat Pemeliharaan
            </a>
            <a href="{{ route('efisiensi.index') }}" class="block p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 font-semibold text-sm">
                <i class="fas fa-tachometer-alt mr-2"></i>Input Efisiensi Kinerja
            </a>
            <a href="{{ route('variabel.index') }}" class="block p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-colors text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 font-semibold text-sm">
                <i class="fas fa-sliders mr-2"></i>Variabel Eksternal
            </a>
            <a href="{{ route('profile.edit') }}" class="block p-3 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-semibold text-sm">
                <i class="fas fa-user-edit mr-2"></i>Ubah Profil Saya
            </a>
        </div>
    </div>

    <!-- Data Entry Completeness Warning list (incompleteAssets) -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-2 dark:text-white">
            <i class="fas fa-exclamation-circle text-rose-500 mr-2"></i>Kelengkapan Data Observasi Aset
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Aset berikut belum lengkap parameternya untuk dapat dilatih atau diprediksi oleh Naive Bayes.</p>
        
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($incompleteAssets as $inc)
                <div class="p-3.5 bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ $inc['nama_brg'] }}</span>
                        <span class="text-[10px] font-semibold text-slate-400 block mt-0.5">{{ $inc['kode_brg'] }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($inc['missing'] as $miss)
                            <span class="inline-flex items-center rounded bg-rose-50 px-2 py-0.5 text-[9px] font-bold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                                Kurang {{ $miss }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fa-solid fa-circle-check text-3xl text-emerald-500 mb-2"></i>
                    <p class="text-sm font-semibold">Semua data observasi aset lengkap!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Status Kondisi Aset -->
    <div class="bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
            <i class="fas fa-chart-pie text-green-500 mr-2"></i>Status Kelayakan Aset (Naive Bayes)
        </h2>
        <div class="space-y-4">
            @php
                $layakPct = $totalAssets > 0 ? round(($distribKondisi['Layak'] / $totalAssets) * 100, 1) : 0;
                $servisPct = $totalAssets > 0 ? round(($distribKondisi['Perlu Servis'] / $totalAssets) * 100, 1) : 0;
                $tidakPct = $totalAssets > 0 ? round(($distribKondisi['Tidak Layak'] / $totalAssets) * 100, 1) : 0;
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Layak</span>
                    <span class="text-green-600 font-bold">{{ $layakPct }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-3">
                    <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ $layakPct }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Perlu Servis</span>
                    <span class="text-amber-600 font-bold">{{ $servisPct }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-3">
                    <div class="bg-amber-500 h-3 rounded-full" style="width: {{ $servisPct }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Tidak Layak</span>
                    <span class="text-rose-600 font-bold">{{ $tidakPct }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-3">
                    <div class="bg-rose-50 h-3 rounded-full !bg-rose-500" style="width: {{ $tidakPct }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Maintenance logs (dynamic) -->
    <div class="bg-white rounded-lg shadow-md p-6 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 mb-4 dark:text-white">
            <i class="fas fa-history text-blue-500 mr-2"></i>Pemeliharaan Terakhir (PM)
        </h2>
        <div class="space-y-3">
            @php
                $recentPMs = App\Models\Pemeliharaan::with('asset')->latest('tgl_pm')->take(3)->get();
            @endphp
            @forelse($recentPMs as $pm)
                <div class="p-3 bg-gray-50 dark:bg-slate-950/20 rounded-lg border-l-4 border-blue-500 dark:border-blue-400">
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $pm->asset->nama_brg ?? '-' }}</p>
                            <p class="text-xs text-gray-500 leading-normal">{{ $pm->jenis_pm }} • {{ $pm->kon_after }}</p>
                        </div>
                        <span class="text-[10px] text-gray-400 font-semibold">{{ $pm->tgl_pm ? $pm->tgl_pm->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    Belum ada riwayat tindakan pemeliharaan yang tercatat.
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
