@extends('layouts.app')
@section('title', 'Prediksi Kelayakan Aset')
@section('content')

<div class="space-y-6" x-data="predictionPage()">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Prediksi Kelayakan Aset</span>
    </nav>

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            <i class="fa-solid fa-wand-magic-sparkles mr-2 text-violet-600"></i>Prediksi Kelayakan Aset
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pilih aset untuk memuat riwayat data observasi terbaru secara otomatis, hitung prediksi kelayakan, dan simpan hasilnya.
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left Panel: Selection and Loaded Data (2 cols) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Selection Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">
                    <i class="fa-solid fa-magnifying-glass mr-2 text-slate-400"></i>Pilih Aset Laboratorium
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="asset_select" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Daftar Aset</label>
                        <select 
                            id="asset_select"
                            x-model="selectedAssetId" 
                            @change="loadAssetHistory"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-950 focus:border-violet-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:focus:border-violet-500"
                        >
                            <option value="">-- Pilih Aset --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id_aset }}">
                                    [{{ $asset->kode_brg }}] {{ $asset->nama_brg }} ({{ $asset->merk_tipe }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- History / Feature Panel --}}
            <div x-show="selectedAssetId && !historyLoading" x-transition class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-file-invoice mr-2 text-slate-500"></i>Riwayat Observasi Terbaru
                    </h3>
                    <span x-show="isIncomplete" x-cloak class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                        <i class="fa-solid fa-circle-exclamation"></i> Data Tidak Lengkap
                    </span>
                    <span x-show="!isIncomplete" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <i class="fa-solid fa-circle-check"></i> Siap Prediksi
                    </span>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Warning message for incomplete --}}
                    <div x-show="isIncomplete" class="rounded-xl border border-rose-100 bg-rose-50/50 p-4 text-xs text-rose-800 dark:border-rose-900/30 dark:bg-rose-950/20 dark:text-rose-300">
                        <div class="flex gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-base mt-0.5"></i>
                            <div>
                                <p class="font-bold">Beberapa data historis tidak ditemukan untuk aset ini!</p>
                                <p class="mt-1 leading-relaxed">Prediksi Naive Bayes memerlukan 10 parameter lengkap dari 4 tabel pendukung (Kondisi Fisik, Pemeliharaan, Efisiensi, & Variabel Eksternal). Harap lengkapi data observasi aset terlebih dahulu sebelum melanjutkan.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Features Grid --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        {{-- 1. Kondisi Fisik --}}
                        <div class="rounded-xl border border-slate-100 p-4 bg-slate-50/50 dark:border-slate-800/50 dark:bg-slate-950/20">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-stethoscope"></i> Kondisi Fisik Aset
                            </p>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Kondisi Barang</span>
                                    <span class="font-bold" :class="historyData.kondisi_brg ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.kondisi_brg || 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Usia Pakai</span>
                                    <span class="font-bold" :class="historyData.usia_pakai !== null ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.usia_pakai !== null ? historyData.usia_pakai + ' tahun' : 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Frekuensi Kerusakan</span>
                                    <span class="font-bold" :class="historyData.frq_kerusakan !== null ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.frq_kerusakan !== null ? historyData.frq_kerusakan + ' kali' : 'Tidak ada'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Pemeliharaan --}}
                        <div class="rounded-xl border border-slate-100 p-4 bg-slate-50/50 dark:border-slate-800/50 dark:bg-slate-950/20">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-screwdriver-wrench"></i> Pemeliharaan & PM
                            </p>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Jenis PM</span>
                                    <span class="font-bold" :class="historyData.jenis_pm ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.jenis_pm || 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Interval PM</span>
                                    <span class="font-bold" :class="historyData.interval_pm !== null ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.interval_pm !== null ? historyData.interval_pm + ' bulan' : 'Tidak ada'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Efisiensi --}}
                        <div class="rounded-xl border border-slate-100 p-4 bg-slate-50/50 dark:border-slate-800/50 dark:bg-slate-950/20">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-gauge-high"></i> Efisiensi & Kinerja
                            </p>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Efisiensi Output</span>
                                    <span class="font-bold" :class="historyData.efi_out ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.efi_out || 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Downtime</span>
                                    <span class="font-bold" :class="historyData.downtime !== null ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.downtime !== null ? historyData.downtime + ' jam' : 'Tidak ada'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Variabel Eksternal --}}
                        <div class="rounded-xl border border-slate-100 p-4 bg-slate-50/50 dark:border-slate-800/50 dark:bg-slate-950/20">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-sliders"></i> Variabel Lingkungan
                            </p>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Kondisi Lingkungan</span>
                                    <span class="font-bold" :class="historyData.lingkungan ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.lingkungan || 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Stabilitas Daya Listrik</span>
                                    <span class="font-bold" :class="historyData.daya_listrik ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.daya_listrik || 'Tidak ada'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Ketersediaan Sparepart</span>
                                    <span class="font-bold" :class="historyData.sparepart ? 'text-slate-900 dark:text-white' : 'text-rose-500'" x-text="historyData.sparepart || 'Tidak ada'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button
                            @click="triggerPrediction"
                            :disabled="isIncomplete || predictLoading"
                            class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-violet-600/25 transition-all hover:bg-violet-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <span x-show="!predictLoading" class="inline-flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-xs mr-1"></i> Proses Prediksi
                            </span>
                            <span x-show="predictLoading" x-cloak class="flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- History Loader Placeholder --}}
            <div x-show="historyLoading" class="rounded-2xl border border-slate-200 bg-white p-12 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex justify-center items-center">
                <div class="flex flex-col items-center gap-3">
                    <svg class="h-8 w-8 animate-spin text-violet-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Memuat data observasi aset...</p>
                </div>
            </div>
        </div>

        {{-- Right Panel: Prediction Output (1 col) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                {{-- Error Alert --}}
                <div x-show="errorMessage" x-transition x-cloak class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-900/30 dark:bg-rose-950/20 dark:text-rose-300">
                    <div class="flex gap-2">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span class="font-medium" x-text="errorMessage"></span>
                    </div>
                </div>

                {{-- Prediction Results Panel --}}
                <div x-show="predictionResult" x-transition class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-chart-simple mr-2 text-violet-600"></i>Hasil Prediksi Naive Bayes
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Class Output --}}
                        <div class="text-center py-4 rounded-xl" :class="getBadgeBackgroundClass()">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-60 mb-1">Hasil Klasifikasi</p>
                            <h2 class="text-2xl font-black" :class="getTextColorClass()" x-text="predictionResult.predicted_class"></h2>
                        </div>

                        {{-- Save DB status --}}
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-950/30 p-3 rounded-lg border border-slate-100 dark:border-slate-800">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>Hasil tersimpan di tabel <strong>t_hasil_prediksi</strong>.</span>
                        </div>

                        {{-- Probabilities distribution --}}
                        <div class="space-y-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Distribusi Probabilitas</p>
                            
                            {{-- Layak Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-semibold">
                                    <span class="text-slate-700 dark:text-slate-300">Layak</span>
                                    <span class="text-slate-900 dark:text-white" x-text="(predictionResult.probabilities.Layak * 100).toFixed(2) + '%'"></span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden dark:bg-slate-800">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="'width: ' + (predictionResult.probabilities.Layak * 100) + '%'"></div>
                                </div>
                            </div>

                            {{-- Perlu Servis Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-semibold">
                                    <span class="text-slate-700 dark:text-slate-300">Perlu Servis</span>
                                    <span class="text-slate-900 dark:text-white" x-text="(predictionResult.probabilities['Perlu Servis'] * 100).toFixed(2) + '%'"></span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden dark:bg-slate-800">
                                    <div class="h-full bg-amber-500 rounded-full transition-all duration-500" :style="'width: ' + (predictionResult.probabilities['Perlu Servis'] * 100) + '%'"></div>
                                </div>
                            </div>

                            {{-- Tidak Layak Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-semibold">
                                    <span class="text-slate-700 dark:text-slate-300">Tidak Layak</span>
                                    <span class="text-slate-900 dark:text-white" x-text="(predictionResult.probabilities['Tidak Layak'] * 100).toFixed(2) + '%'"></span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden dark:bg-slate-800">
                                    <div class="h-full bg-rose-500 rounded-full transition-all duration-500" :style="'width: ' + (predictionResult.probabilities['Tidak Layak'] * 100) + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Empty Results State --}}
                <div x-show="!predictionResult" class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 dark:bg-slate-950/40 mx-auto mb-4">
                        <i class="fa-solid fa-wand-magic-sparkles text-xl text-slate-400 dark:text-slate-600"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Hasil Prediksi</p>
                    <p class="mt-1 text-xs text-slate-400">Pilih aset dan lakukan proses prediksi untuk menampilkan hasil kalkulasi kelayakan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function predictionPage() {
    return {
        selectedAssetId: '',
        historyLoading: false,
        predictLoading: false,
        isIncomplete: false,
        errorMessage: null,
        predictionResult: null,
        historyData: {
            kondisi_brg: null,
            usia_pakai: null,
            frq_kerusakan: null,
            jenis_pm: null,
            interval_pm: null,
            efi_out: null,
            downtime: null,
            lingkungan: null,
            daya_listrik: null,
            sparepart: null
        },

        async loadAssetHistory() {
            if (!this.selectedAssetId) {
                this.resetState();
                return;
            }

            this.historyLoading = true;
            this.errorMessage = null;
            this.predictionResult = null;

            try {
                const response = await fetch(`/prediksi-naive-bayes/asset-history/${this.selectedAssetId}`);
                const res = await response.json();
                
                if (res.success) {
                    this.historyData = res.data;
                    this.isIncomplete = res.incomplete;
                } else {
                    this.errorMessage = 'Gagal mengambil data historis aset.';
                }
            } catch (err) {
                this.errorMessage = 'Gagal terhubung dengan server.';
            } finally {
                this.historyLoading = false;
            }
        },

        async triggerPrediction() {
            if (this.isIncomplete || !this.selectedAssetId) return;

            this.predictLoading = true;
            this.errorMessage = null;
            this.predictionResult = null;

            try {
                const bodyPayload = {
                    id_aset: parseInt(this.selectedAssetId),
                    ...this.historyData
                };

                const response = await fetch('{{ route("prediksi.predict") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bodyPayload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.predictionResult = data;
                } else {
                    this.errorMessage = data.message || 'Gagal menghitung prediksi kelayakan aset.';
                }
            } catch (err) {
                this.errorMessage = 'Gagal terhubung dengan API Prediksi. Pastikan Flask API dalam kondisi aktif.';
            } finally {
                this.predictLoading = false;
            }
        },

        getBadgeBackgroundClass() {
            if (!this.predictionResult) return 'bg-slate-50 text-slate-700';
            const label = this.predictionResult.predicted_class;
            if (label === 'Layak') return 'bg-emerald-50 dark:bg-emerald-500/10';
            if (label === 'Perlu Servis') return 'bg-amber-50 dark:bg-amber-500/10';
            if (label === 'Tidak Layak') return 'bg-rose-50 dark:bg-rose-500/10';
            return 'bg-slate-50 dark:bg-slate-800/40';
        },

        getTextColorClass() {
            if (!this.predictionResult) return 'text-slate-700';
            const label = this.predictionResult.predicted_class;
            if (label === 'Layak') return 'text-emerald-700 dark:text-emerald-400';
            if (label === 'Perlu Servis') return 'text-amber-700 dark:text-amber-400';
            if (label === 'Tidak Layak') return 'text-rose-700 dark:text-rose-400';
            return 'text-slate-700 dark:text-slate-300';
        },

        resetState() {
            this.isIncomplete = false;
            this.predictionResult = null;
            this.errorMessage = null;
            this.historyData = {
                kondisi_brg: null,
                usia_pakai: null,
                frq_kerusakan: null,
                jenis_pm: null,
                interval_pm: null,
                efi_out: null,
                downtime: null,
                lingkungan: null,
                daya_listrik: null,
                sparepart: null
            };
        }
    };
}
</script>
@endpush
