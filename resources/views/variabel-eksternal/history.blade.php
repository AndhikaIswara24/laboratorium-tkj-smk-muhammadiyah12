@extends('layouts.app')
@section('title', 'Riwayat Variabel Eksternal Aset')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('variabel.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-sliders mr-1"></i>Variabel Eksternal
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Riwayat Aset</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-600"></i>Riwayat Variabel Eksternal Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Lacak parameter eksternal (lingkungan, pasokan daya, sparepart, anggaran, dan efek) secara historis untuk aset ini.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('variabel.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('variabel.create', ['id_aset' => $asset->id_aset]) }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Observasi
            </a>
        </div>
    </div>

    {{-- Success Alert --}}
    @if ($message = Session::get('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200" x-data="{ show: true }" x-show="show" x-transition>
            <i class="fa-solid fa-check-circle text-lg"></i>
            <span class="flex-1 text-sm font-medium">{{ $message }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-300 dark:hover:text-emerald-100">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Asset Detail Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        {{-- Card Header --}}
        <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                    <i class="fa-solid fa-cube text-lg text-white"></i>
                </span>
                <div>
                    <h2 class="text-base font-bold text-white">{{ $asset->nama_brg }}</h2>
                    <p class="text-xs text-blue-100">Kode Aset: <span class="font-mono font-bold">{{ $asset->kode_brg }}</span></p>
                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Detail 1: Merk/Tipe --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Merk / Tipe</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">{{ $asset->merk_tipe ?? '—' }}</p>
                </div>

                {{-- Detail 2: Lokasi --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Lokasi / Lab</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-location-dot mr-1 text-rose-500"></i>{{ $asset->lokasi ?? '—' }}
                    </p>
                </div>

                {{-- Detail 3: Tahun & Usia Pakai --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tahun & Usia Pakai</p>
                    @php
                        $usia = $asset->thn_perolehan ? (int)date('Y') - (int)$asset->thn_perolehan : null;
                    @endphp
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        {{ $asset->thn_perolehan ?? '—' }}
                        @if($usia !== null)
                            <span class="text-xs font-normal text-slate-500">({{ $usia }} tahun)</span>
                        @endif
                    </p>
                </div>

                {{-- Detail 4: Harga Perolehan --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Harga Perolehan</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        {{ $asset->harga_perolehan ? 'Rp ' . number_format($asset->harga_perolehan, 0, ',', '.') : '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Riwayat Observasi Variabel Eksternal</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Tanggal Observasi</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Lingkungan</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Daya Listrik</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Sparepart</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Anggaran</th>
                        <th class="px-6 py-3.5 font-semibold text-slate-600 dark:text-slate-300">Efek Eksternal (Ext Effect)</th>
                        <th class="px-6 py-3.5 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 text-slate-500">{{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
                                    {{ $row->tgl_observasi ? $row->tgl_observasi->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $lingkunganClass = match($row->lingkungan) {
                                        'Baik' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Cukup' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Buruk' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $lingkunganClass }}">
                                    {{ $row->lingkungan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $dayaClass = match($row->daya_listrik) {
                                        'Stabil' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Tidak Stabil' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Sering Padam' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $dayClass }}">
                                    {{ $row->daya_listrik }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $sparepartClass = match($row->sparepart) {
                                        'Tersedia' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Terbatas' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Tidak Ada' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $sparepartClass }}">
                                    {{ $row->sparepart }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $anggaranClass = match($row->anggaran) {
                                        'Mendukung' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Terbatas' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Tidak Ada' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $anggaranClass }}">
                                    {{ $row->anggaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $effectClass = match($row->ext_effect) {
                                        'Rendah' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Sedang' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Tinggi' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $effectClass }}">
                                    {{ $row->ext_effect }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('variabel.edit', $row->id_eksternal) }}" class="btn-secondary px-2.5 py-1.5 text-xs" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('variabel.destroy', $row->id_eksternal) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-950 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-rose-500/10" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada riwayat observasi untuk aset ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rows->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-6 py-3 dark:border-slate-800 dark:bg-slate-800/20">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
