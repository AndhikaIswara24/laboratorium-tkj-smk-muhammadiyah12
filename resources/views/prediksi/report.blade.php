@extends('layouts.app')
@section('title', 'Laporan Kelayakan Aset')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Laporan Kelayakan</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-file-invoice mr-2 text-amber-600"></i>Laporan Kelayakan Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Laporan resmi status kelayakan aset hasil kalkulasi model Naive Bayes beserta rekomendasi tindakan.
            </p>
        </div>
        
        {{-- Export Actions Panel --}}
        <div class="flex flex-wrap gap-2">
            {{-- Print PDF --}}
            <a 
                href="{{ route('prediksi.laporan_kelayakan', array_merge(request()->all(), ['export' => 'print'])) }}" 
                target="_blank" 
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 transition shadow-sm"
            >
                <i class="fa-solid fa-print text-rose-500"></i> Cetak / PDF
            </a>

            {{-- Export Excel --}}
            <a 
                href="{{ route('prediksi.laporan_kelayakan', array_merge(request()->all(), ['export' => 'excel'])) }}" 
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 transition shadow-sm"
            >
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Ekspor Excel
            </a>

            {{-- Export CSV --}}
            <a 
                href="{{ route('prediksi.laporan_kelayakan', array_merge(request()->all(), ['export' => 'csv'])) }}" 
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 transition shadow-sm"
            >
                <i class="fa-solid fa-file-csv text-blue-500"></i> Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Recommendations/Category Totals Deck --}}
    <div class="grid gap-6 sm:grid-cols-3">
        {{-- Eligible / Continue Use --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5 mb-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </span>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Teruskan Penggunaan</p>
                    <p class="text-lg font-black text-slate-900 dark:text-white">{{ $totalPredicted - $needsService - $notEligible }} Aset</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 leading-relaxed border-t border-slate-100 pt-2.5 dark:border-slate-800/80">
                <strong>Rekomendasi:</strong> Aset dalam kondisi optimal. Teruskan pemeliharaan rutin preventif.
            </p>
        </div>

        {{-- Needs Service / Schedule Maintenance --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5 mb-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                </span>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Jadwalkan Pemeliharaan</p>
                    <p class="text-lg font-black text-amber-600">{{ $needsService }} Aset</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 leading-relaxed border-t border-slate-100 pt-2.5 dark:border-slate-800/80">
                <strong>Rekomendasi:</strong> Hubungi tim teknisi / pelaksana untuk servis perbaikan korektif segera.
            </p>
        </div>

        {{-- Not Eligible / Replace or Dispose --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5 mb-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-trash-can text-lg"></i>
                </span>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Ganti / Hapus Aset</p>
                    <p class="text-lg font-black text-rose-600">{{ $notEligible }} Aset</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 leading-relaxed border-t border-slate-100 pt-2.5 dark:border-slate-800/80">
                <strong>Rekomendasi:</strong> Aset rusak berat / tidak efisien. Jadwalkan pengadaan pengganti baru.
            </p>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="{{ route('prediksi.laporan_kelayakan') }}" method="GET" class="grid gap-4 md:grid-cols-3 items-end">
            {{-- Filter Label --}}
            <div>
                <label for="filter_label" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Filter Kelayakan</label>
                <select 
                    id="filter_label"
                    name="label" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-950 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:focus:border-amber-500"
                >
                    <option value="">-- Semua Kelayakan --</option>
                    <option value="Layak" {{ request('label') === 'Layak' ? 'selected' : '' }}>Layak</option>
                    <option value="Perlu Servis" {{ request('label') === 'Perlu Servis' ? 'selected' : '' }}>Perlu Servis</option>
                    <option value="Tidak Layak" {{ request('label') === 'Tidak Layak' ? 'selected' : '' }}>Tidak Layak</option>
                </select>
            </div>

            {{-- Filter Lokasi --}}
            <div>
                <label for="filter_location" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Filter Lokasi</label>
                <select 
                    id="filter_location"
                    name="location" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-950 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:focus:border-amber-500"
                >
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Form Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-amber-700 transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
                @if(request()->filled('label') || request()->filled('location'))
                    <a href="{{ route('prediksi.laporan_kelayakan') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main predictions table list --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300 w-12 text-center">#</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Aset</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Lokasi</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Tgl Prediksi</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Status Kelayakan</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Probabilitas</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Rekomendasi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($predictions as $item)
                        @php
                            $topProb = max($item->prob_layak, $item->prob_servis, $item->prob_tidak_layak) * 100;
                            
                            $rowHighlight = '';
                            $badgeClass = '';
                            $recomBadge = '';
                            $recomText = '';

                            if ($item->hasil_prediksi === 'Layak') {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400';
                                $recomBadge = 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-300';
                                $recomText = 'Teruskan Penggunaan (Continue Use)';
                            } elseif ($item->hasil_prediksi === 'Perlu Servis') {
                                $rowHighlight = 'bg-amber-50/20 dark:bg-amber-950/5';
                                $badgeClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400';
                                $recomBadge = 'bg-amber-50 text-amber-800 dark:bg-amber-950/20 dark:text-amber-300';
                                $recomText = 'Jadwalkan Pemeliharaan (Schedule Maintenance)';
                            } elseif ($item->hasil_prediksi === 'Tidak Layak') {
                                $rowHighlight = 'bg-rose-50/20 dark:bg-rose-950/5';
                                $badgeClass = 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400';
                                $recomBadge = 'bg-rose-50 text-rose-800 dark:bg-rose-950/20 dark:text-rose-300';
                                $recomText = 'Ganti / Hapus Aset (Replace/Dispose)';
                            }
                        @endphp
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40 {{ $rowHighlight }}">
                            <td class="px-4 py-4 text-center text-slate-400 font-medium">
                                {{ ($predictions->currentPage()-1) * $predictions->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item->asset->nama_brg ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ $item->asset->kode_brg ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $item->asset->lokasi ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-slate-500">
                                {{ $item->tgl_prediksi ? $item->tgl_prediksi->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold {{ $badgeClass }}">
                                    {{ $item->hasil_prediksi }}
                                </span>
                            </td>
                            <td class="px-4 py-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ number_format($topProb, 2) }}%
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $recomBadge }}">
                                    @if($item->hasil_prediksi === 'Layak')
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                    @elseif($item->hasil_prediksi === 'Perlu Servis')
                                        <i class="fa-solid fa-screwdriver-wrench text-[10px]"></i>
                                    @elseif($item->hasil_prediksi === 'Tidak Layak')
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    @endif
                                    {{ $recomText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-4xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-sm font-semibold">Tidak ada data kelayakan.</p>
                                    <p class="text-xs text-slate-400">Silakan lakukan proses kalkulasi prediksi pada data observasi baru terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($predictions->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-800/20">
                {{ $predictions->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
