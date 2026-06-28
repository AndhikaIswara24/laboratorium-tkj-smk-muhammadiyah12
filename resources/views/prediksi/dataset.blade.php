@extends('layouts.app')
@section('title', 'Dataset Naive Bayes')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Dataset Flat</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-database mr-2 text-blue-600"></i>Dataset Flat Naive Bayes
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Data hasil penggabungan (JOIN) dari 4 tabel historis berdasarkan observasi terbaru per aset.
            </p>
        </div>
        <form action="{{ route('prediksi.dataset.generate') }}" method="POST" class="inline-block"
              onsubmit="return confirm('Apakah Anda yakin ingin memproses ulang penggabungan dataset? Data dataset lama akan dibersihkan.')">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-rotate mr-1"></i> Generate Dataset
            </button>
        </form>
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

    {{-- Statistics Deck --}}
    <div class="grid gap-6 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalAssets }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Master Aset</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-table-list text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalDataset }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Baris Dataset Lengkap</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-rose-600">{{ $incompleteCount }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aset Belum Lengkap Datanya</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Preview Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Preview Dataset (t_naive_bayes_dataset)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Aset</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Kondisi</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Usia Pakai</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Freq Rusak</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Jenis PM</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Interval PM</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Efi Output</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Downtime</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Lingkungan</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Daya Listrik</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Sparepart</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Kelas Label</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 dark:text-slate-300">Tgl Generate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-3 py-3 text-slate-500">{{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}</td>
                            <td class="px-3 py-3">
                                <div class="font-bold text-blue-600 dark:text-blue-400">{{ $row->asset->kode_brg ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500 truncate max-w-[120px]">{{ $row->asset->nama_brg ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-3">
                                @php
                                    $kondisiClass = match($row->kondisi_brg) {
                                        'B' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'RR' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        'RB' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $kondisiClass }}">
                                    {{ $row->kondisi_brg }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-semibold">{{ $row->usia_pakai ?? 0 }} thn</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-semibold">{{ $row->frq_kerusakan ?? 0 }} kali</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->jenis_pm }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->interval_pm ?? 0 }} bln</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->efi_out }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-semibold">{{ $row->downtime ?? 0 }} jam</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->lingkungan }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->daya_listrik }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ $row->sparepart }}</td>
                            <td class="px-3 py-3">
                                @php
                                    $labelClass = match($row->kelas_label) {
                                        'Layak' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Perlu Servis' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Tidak Layak' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold ring-1 ring-inset {{ $labelClass }}">
                                    {{ $row->kelas_label }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-slate-500">{{ $row->tgl_input }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-4xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-sm font-semibold">Tabel dataset kosong / belum digenerate.</p>
                                    <p class="text-xs text-slate-400 max-w-md">Klik tombol "Generate Dataset" di atas untuk menggabungkan data terbaru dari 4 tabel historis secara otomatis.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rows->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-800/20">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
