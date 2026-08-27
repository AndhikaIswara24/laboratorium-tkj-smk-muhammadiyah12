@extends('layouts.app')
@section('title', 'Ringkasan Hasil Prediksi')
@section('content')

<div class="space-y-6" x-data="batchPrediction()">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Ringkasan Prediksi</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-list-check mr-2 text-sky-600"></i>Ringkasan Hasil Prediksi
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Rekapitulasi hasil klasifikasi kelayakan aset terbaru berdasarkan pengujian model Naive Bayes.
            </p>
        </div>
        
        {{-- Batch Predict Trigger Button --}}
        <button 
            @click="openModal" 
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-violet-600/25 transition-all hover:shadow-xl hover:shadow-violet-600/30 hover:opacity-95"
        >
            <i class="fa-solid fa-wand-magic-sparkles"></i> Prediksi Massal Aset
        </button>
    </div>

    {{-- Stats Cards Deck --}}
    <div class="grid gap-6 sm:grid-cols-3">
        {{-- Total Predicted --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalPredicted }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Aset Terprediksi</p>
                </div>
            </div>
        </div>

        {{-- Needs Service (Perlu Servis) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $needsService }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Aset Perlu Servis</p>
                </div>
            </div>
        </div>

        {{-- Not Eligible (Tidak Layak) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-circle-xmark text-xl"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-rose-600">{{ $notEligible }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Aset Tidak Layak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="{{ route('prediksi.summary') }}" method="GET" class="grid gap-4 md:grid-cols-3 items-end">
            {{-- Filter Label --}}
            <div>
                <label for="filter_label" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Saring Hasil Prediksi</label>
                <select 
                    id="filter_label"
                    name="label" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-950 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:focus:border-sky-500"
                >
                    <option value="">-- Semua Label --</option>
                    <option value="Layak" {{ request('label') === 'Layak' ? 'selected' : '' }}>Layak</option>
                    <option value="Perlu Servis" {{ request('label') === 'Perlu Servis' ? 'selected' : '' }}>Perlu Servis</option>
                    <option value="Tidak Layak" {{ request('label') === 'Tidak Layak' ? 'selected' : '' }}>Tidak Layak</option>
                </select>
            </div>

            {{-- Filter Lokasi --}}
            <div>
                <label for="filter_location" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Saring Lokasi Aset</label>
                <select 
                    id="filter_location"
                    name="location" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-950 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:focus:border-sky-500"
                >
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-sky-700 transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
                @if(request()->filled('label') || request()->filled('location'))
                    <a href="{{ route('prediksi.summary') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Predictions List --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300 w-12 text-center">#</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Aset</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Lokasi</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Tgl Prediksi</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Hasil Kelayakan</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300">Probabilitas Terbesar</th>
                        <th class="px-4 py-3.5 font-bold text-slate-600 dark:text-slate-300 text-center">Tautan Riwayat Observasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($predictions as $item)
                        @php
                            $topProb = max($item->prob_layak, $item->prob_servis, $item->prob_tidak_layak) * 100;
                            
                            // Highlight styles
                            $rowHighlight = '';
                            $badgeClass = 'bg-slate-50 text-slate-700 dark:bg-slate-800/40 dark:text-slate-300';
                            
                            if ($item->hasil_prediksi === 'Layak') {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400';
                            } elseif ($item->hasil_prediksi === 'Perlu Servis') {
                                $rowHighlight = 'bg-amber-50/30 dark:bg-amber-950/10';
                                $badgeClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 animate-pulse-subtle';
                            } elseif ($item->hasil_prediksi === 'Tidak Layak') {
                                $rowHighlight = 'bg-rose-50/30 dark:bg-rose-950/10';
                                $badgeClass = 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 animate-pulse-subtle';
                            }
                        @endphp
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40 {{ $rowHighlight }}">
                            <td class="px-4 py-4 text-center text-slate-400 font-medium">
                                {{ ($predictions->currentPage()-1) * $predictions->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item->asset->nama_brg ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ $item->asset->kode_brg ?? '-' }} ({{ $item->asset->merk_tipe ?? '-' }})</div>
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $item->asset->lokasi ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-slate-500">
                                {{ $item->tgl_prediksi ? $item->tgl_prediksi->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold {{ $badgeClass }}">
                                    @if($item->hasil_prediksi === 'Perlu Servis')
                                        <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i>
                                    @elseif($item->hasil_prediksi === 'Tidak Layak')
                                        <i class="fa-solid fa-circle-xmark mr-1 text-[10px]"></i>
                                    @endif
                                    {{ $item->hasil_prediksi }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all"
                                             :class="{
                                                 'bg-emerald-500': '{{ $item->hasil_prediksi }}' === 'Layak',
                                                 'bg-amber-500': '{{ $item->hasil_prediksi }}' === 'Perlu Servis',
                                                 'bg-rose-500': '{{ $item->hasil_prediksi }}' === 'Tidak Layak'
                                             }"
                                             style="width: {{ $topProb }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($topProb, 2) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-center items-center gap-1.5">
                                    {{-- Link Kondisi Fisik --}}
                                    <a href="{{ route('kondisi.history', $item->id_aset) }}" 
                                       title="Riwayat Kondisi Fisik"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-violet-50 hover:text-violet-600 hover:border-violet-200 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-violet-500/10 dark:hover:text-violet-400 transition shadow-sm">
                                        <i class="fa-solid fa-stethoscope text-xs"></i>
                                    </a>
                                    
                                    {{-- Link Pemeliharaan --}}
                                    <a href="{{ route('pemeliharaan.history', $item->id_aset) }}" 
                                       title="Riwayat Pemeliharaan"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400 transition shadow-sm">
                                        <i class="fa-solid fa-screwdriver-wrench text-xs"></i>
                                    </a>

                                    {{-- Link Efisiensi --}}
                                    <a href="{{ route('efisiensi.history', $item->id_aset) }}" 
                                       title="Riwayat Efisiensi"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-sky-500/10 dark:hover:text-sky-400 transition shadow-sm">
                                        <i class="fa-solid fa-gauge-high text-xs"></i>
                                    </a>

                                    {{-- Link Variabel Eksternal --}}
                                    <a href="{{ route('variabel.history', $item->id_aset) }}" 
                                       title="Riwayat Variabel Eksternal"
                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400 dark:hover:bg-amber-500/10 dark:hover:text-amber-400 transition shadow-sm">
                                        <i class="fa-solid fa-sliders text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-4xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-sm font-semibold">Tidak ada data prediksi kelayakan.</p>
                                    <p class="text-xs text-slate-400">Pilih menu "Prediksi Kelayakan Aset" untuk mulai melakukan klasifikasi pada data observasi baru.</p>
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

    {{-- Interactive Batch Prediction Progress Modal --}}
    <div 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        {{-- Modal Dialog --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md p-6"
                @click.away="!predicting && (modalOpen = false)"
            >
                {{-- Modal Header --}}
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-violet-600"></i> Prediksi Massal Naive Bayes
                    </h3>
                    <button 
                        x-show="!predicting" 
                        @click="modalOpen = false" 
                        class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Modal Body: Step 1 Ready --}}
                <div x-show="step === 'ready'" class="space-y-4 text-center py-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400 mx-auto">
                        <i class="fa-solid fa-calculator text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Kalkulasi Massal Siap Dijalankan</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                            Sistem akan mengambil seluruh baris data di flat dataset, mengirimkannya ke Flask API Naive Bayes untuk dihitung kelayakannya, dan menyimpan hasilnya.
                        </p>
                    </div>
                    <div class="pt-2">
                        <button 
                            @click="startBatch" 
                            class="w-full rounded-xl bg-violet-600 py-3 text-xs font-bold text-white shadow-lg shadow-violet-600/20 hover:bg-violet-700 transition"
                        >
                            Mulai Proses Prediksi
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Step 2 Processing --}}
                <div x-show="step === 'processing'" class="space-y-5 py-4">
                    <div class="text-center">
                        <div class="relative h-12 w-12 mx-auto mb-3">
                            <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-violet-600 animate-spin dark:border-slate-800 dark:border-t-violet-400"></div>
                        </div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Memproses kalkulasi kelayakan...</h4>
                        <p class="text-[10px] text-slate-400 mt-1 truncate" x-text="'Memprediksi: ' + currentAssetName"></p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[10px] font-bold text-slate-500">
                            <span x-text="'Tahapan Kalkulasi'"></span>
                            <span x-text="progress + '%'"></span>
                        </div>
                        <div class="h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-violet-600 to-indigo-600 rounded-full transition-all duration-300"
                                :style="'width: ' + progress + '%'"
                            ></div>
                        </div>
                    </div>
                </div>

                {{-- Modal Body: Step 3 Done --}}
                <div x-show="step === 'done'" class="space-y-5 py-2">
                    <div class="text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 mx-auto mb-3">
                            <i class="fa-solid fa-circle-check text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Kalkulasi Massal Selesai!</h4>
                        <p class="text-xs text-slate-400 mt-1">Hasil kalkulasi kelayakan seluruh aset.</p>
                    </div>

                    {{-- Summary Totals Deck inside Modal --}}
                    <div class="grid grid-cols-3 gap-2.5 pt-2">
                        <div class="bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl p-3 text-center border border-emerald-100/50 dark:border-emerald-900/30">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block" x-text="totals.Layak"></span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium">Layak</span>
                        </div>
                        <div class="bg-amber-50/50 dark:bg-amber-950/20 rounded-xl p-3 text-center border border-amber-100/50 dark:border-amber-900/30">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 block" x-text="totals['Perlu Servis']"></span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium">Servis</span>
                        </div>
                        <div class="bg-rose-50/50 dark:bg-rose-950/20 rounded-xl p-3 text-center border border-rose-100/50 dark:border-rose-900/30">
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 block" x-text="totals['Tidak Layak']"></span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400 font-medium">Tidak Layak</span>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button 
                            @click="refreshPage" 
                            class="w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800 transition dark:bg-slate-950 dark:hover:bg-slate-900"
                        >
                            Tutup & Muat Ulang Daftar
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Error State --}}
                <div x-show="errorMessage" x-transition class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-[11px] text-rose-800 dark:border-rose-900/30 dark:bg-rose-950/20 dark:text-rose-300">
                    <div class="flex gap-2">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span class="font-medium" x-text="errorMessage"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Inline styles to support pulse effects on highlighted records --}}
<style>
@keyframes pulse-subtle {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}
.animate-pulse-subtle {
    animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection

@push('scripts')
<script>
function batchPrediction() {
    return {
        modalOpen: false,
        step: 'ready',
        predicting: false,
        items: [],
        currentIndex: 0,
        currentAssetName: '',
        progress: 0,
        errorMessage: null,
        totals: {
            Layak: 0,
            'Perlu Servis': 0,
            'Tidak Layak': 0
        },

        openModal() {
            this.modalOpen = true;
            this.step = 'ready';
            this.predicting = false;
            this.errorMessage = null;
            this.items = [];
            this.currentIndex = 0;
            this.progress = 0;
            this.totals = { Layak: 0, 'Perlu Servis': 0, 'Tidak Layak': 0 };
        },

        async startBatch() {
            this.predicting = true;
            this.step = 'processing';
            this.errorMessage = null;
            this.progress = 0;
            this.currentAssetName = 'Mengambil data dari database...';

            const interval = setInterval(() => {
                if (this.progress < 25) {
                    this.progress += 5;
                    this.currentAssetName = 'Mengambil dataset flat...';
                } else if (this.progress < 75) {
                    this.progress += 2;
                    this.currentAssetName = 'Mengirim payload batch & memproses inferensi di Flask...';
                } else if (this.progress < 95) {
                    this.progress += 1;
                    this.currentAssetName = 'Menyimpan hasil bulk insert ke database...';
                }
            }, 150);

            try {
                const response = await fetch('{{ route("prediksi.predict_all_optimized") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                clearInterval(interval);
                this.progress = 100;

                const res = await response.json();

                if (response.ok && res.success) {
                    this.totals = res.totals;
                    this.items = { length: res.total_processed };
                    this.step = 'done';
                } else {
                    this.errorMessage = res.message || 'Gagal memproses kalkulasi massal.';
                    this.step = 'ready';
                }
            } catch (err) {
                clearInterval(interval);
                this.errorMessage = 'Terjadi kesalahan jaringan atau Flask API tidak aktif.';
                this.step = 'ready';
            } finally {
                this.predicting = false;
            }
        },

        refreshPage() {
            window.location.reload();
        }
    };
}
</script>
@endpush
