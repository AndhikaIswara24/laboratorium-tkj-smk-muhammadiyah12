@extends('layouts.app')
@section('title', 'Data Aset')
@section('content')

<div class="space-y-6" x-data="{ showImport: false }">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-boxes-stacked mr-2 text-blue-600"></i>Data Master Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola data inventaris aset, spesifikasi, lokasi, dan asal usul perolehan barang laboratorium TKJ.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="showImport = !showImport" class="btn-secondary">
                <i class="fa-solid fa-file-import text-emerald-600 dark:text-emerald-400"></i> Import Aset (CSV)
            </button>
            <a href="{{ route('assets.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Aset
            </a>
        </div>
    </div>

    {{-- Success/Error Alerts --}}
    @if ($message = Session::get('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200" x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-check-circle text-lg"></i>
            <span class="flex-1 text-sm font-medium">{{ $message }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-300 dark:hover:text-emerald-100">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200" x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-circle-exclamation text-lg"></i>
            <span class="flex-1 text-sm font-medium">{{ $message }}</span>
            <button @click="show = false" class="text-rose-600 hover:text-rose-800 dark:text-rose-300 dark:hover:text-rose-100">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- CSV Import Dropzone Form --}}
    <div x-cloak x-show="showImport" x-transition class="rounded-xl border border-blue-200 bg-blue-50/55 p-6 dark:border-blue-500/20 dark:bg-blue-500/5">
        <div class="flex flex-col md:flex-row gap-6 justify-between">
            <div class="space-y-2">
                <h3 class="text-base font-bold text-blue-900 dark:text-blue-200">
                    <i class="fa-solid fa-file-excel mr-1"></i> Petunjuk Import Data Aset (CSV)
                </h3>
                <p class="text-sm text-blue-800 dark:text-blue-300 max-w-xl leading-relaxed">
                    Unggah file CSV dengan header kolom berikut: 
                    <code class="block mt-2 p-2 bg-white dark:bg-slate-900 rounded font-mono text-xs select-all border border-blue-100 dark:border-slate-800">
                        kode_brg, nama_brg, merk_tipe, spesifikasi, lokasi, thn_perolehan, harga_perolehan, asal_usul
                    </code>
                    * Kolom <span class="font-bold">kode_brg</span> (harus unik) & <span class="font-bold">nama_brg</span> wajib diisi. Kolom <span class="font-bold">asal_usul</span> diisi salah satu dari: <span class="font-semibold">Pembelian, Hibah, Dropping Dinas, Dana BOS</span>.
                </p>
            </div>
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-3 self-center md:self-end">
                @csrf
                <div class="space-y-1">
                    <label for="csv_file" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih File CSV</label>
                    <input type="file" id="csv_file" name="file" accept=".csv,.txt" required
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-slate-300">
                </div>
                <button type="submit" class="btn-primary py-2.5">
                    <i class="fa-solid fa-upload"></i> Unggah
                </button>
            </form>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" action="{{ route('assets.index') }}" class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cari Aset</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="search" name="search" placeholder="Cari Kode, Nama, atau Merk/Tipe..." value="{{ old('search', $search ?? '') }}"
                           class="form-control w-full py-2.5 pl-9 pr-3">
                </div>
            </div>
            <div>
                <label for="asal_usul" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Filter Asal Usul</label>
                <div class="flex gap-2">
                    <select id="asal_usul" name="asal_usul" class="form-control w-full py-2.5">
                        <option value="">-- Semua --</option>
                        <option value="Pembelian" @selected(($filter_asal ?? '') === 'Pembelian')>Pembelian</option>
                        <option value="Hibah" @selected(($filter_asal ?? '') === 'Hibah')>Hibah</option>
                        <option value="Dropping Dinas" @selected(($filter_asal ?? '') === 'Dropping Dinas')>Dropping Dinas</option>
                        <option value="Dana BOS" @selected(($filter_asal ?? '') === 'Dana BOS')>Dana BOS</option>
                    </select>
                    <button type="submit" class="btn-primary" title="Cari">
                        <i class="fa-solid fa-search"></i>
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn-secondary" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistics & Chart Grid --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Stats cards block --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <i class="fa-solid fa-cubes text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $items->total() }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Aset Ditemukan</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600">
                            Rp {{ number_format(collect($items->items())->sum('harga_perolehan'), 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total Nilai (Halaman Ini)</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                        <i class="fa-solid fa-industry text-lg"></i>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-violet-600">
                            {{ count(array_filter(collect($items->items())->pluck('lokasi')->toArray())) }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Variasi Lokasi Penempatan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribution Chart Panel --}}
        <div class="lg:col-span-2">
            <x-dashboard.panel title="Distribusi Asal Usul Aset" subtitle="Proporsi perolehan inventaris barang" icon="fa-chart-pie">
                <div class="h-64"><canvas id="asalChart"></canvas></div>
            </x-dashboard.panel>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Kode Barang</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Nama Barang</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Merk/Tipe</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Lokasi / Lab</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Tahun Perolehan</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Harga Perolehan</th>
                        <th class="px-4 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Asal Usul</th>
                        <th class="px-4 py-3.5 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($items as $item)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-4 text-slate-500">{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-4 font-semibold text-blue-600 dark:text-blue-300">{{ $item->kode_brg }}</td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $item->nama_brg }}</div>
                                @if($item->spesifikasi)
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[200px]" title="{{ $item->spesifikasi }}">
                                        {{ $item->spesifikasi }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $item->merk_tipe ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1 text-slate-700 dark:text-slate-300 font-medium">
                                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                                    {{ $item->lokasi ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-700 dark:text-slate-300 font-medium">{{ $item->thn_perolehan ?? '—' }}</td>
                            <td class="px-4 py-4">
                                @if($item->harga_perolehan)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $asalClass = match($item->asal_usul) {
                                        'Pembelian' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-300',
                                        'Hibah' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Dropping Dinas' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Dana BOS' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $asalClass }}">
                                    {{ $item->asal_usul }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('assets.edit', $item->id_aset) }}" class="btn-secondary px-2.5 py-1.5 text-xs" title="Edit Aset">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $item->id_aset) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data aset ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-950 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-rose-500/10" title="Hapus Aset">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-boxes-stacked text-4xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-sm font-medium">Belum ada data aset terdaftar.</p>
                                    <a href="{{ route('assets.create') }}" class="btn-primary mt-2">
                                        <i class="fa-solid fa-plus"></i> Tambah Aset Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-800/20">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartText = document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569';
    Chart.defaults.color = chartText;
    Chart.defaults.borderColor = 'rgba(148, 163, 184, .25)';

    new Chart(document.getElementById('asalChart'), {
        type: 'doughnut',
        data: { 
            labels: ['Pembelian', 'Hibah', 'Dropping Dinas', 'Dana BOS'], 
            datasets: [{ 
                data: [
                    {{ $distribAsal['Pembelian'] }}, 
                    {{ $distribAsal['Hibah'] }}, 
                    {{ $distribAsal['Dropping Dinas'] }}, 
                    {{ $distribAsal['Dana BOS'] }}
                ], 
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'], 
                borderWidth: 0 
            }] 
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '68%', 
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                } 
            } 
        }
    });
});
</script>
@endpush

@endsection
