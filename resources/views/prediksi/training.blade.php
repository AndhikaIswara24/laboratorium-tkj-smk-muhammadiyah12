@extends('layouts.app')
@section('title', 'Training Model Naive Bayes')
@section('content')

<div class="space-y-6" x-data="trainingPage()">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Training Model</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-graduation-cap mr-2 text-emerald-600"></i>Training Model
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Latih model Gaussian Naive Bayes dari dataset dan evaluasi performa klasifikasi.
            </p>
        </div>
        <button
            id="btn-train"
            @click="startTraining"
            :disabled="loading"
            class="group relative inline-flex items-center gap-2 overflow-hidden rounded-xl bg-emerald-600 bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition-all hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-600/30 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-slate-950"
        >
            {{-- Shimmer effect --}}
            <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>
            
            <span x-show="!loading" class="relative flex items-center gap-2">
                <i class="fa-solid fa-play text-xs"></i> Mulai Training
            </span>

            <span x-show="loading" x-cloak class="relative flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Melatih Model…
            </span>
        </button>
    </div>

    {{-- Loading Overlay --}}
    <div x-show="loading" x-transition.opacity class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col items-center justify-center gap-4 py-20">
            <div class="relative">
                <div class="h-16 w-16 animate-spin rounded-full border-4 border-slate-200 border-t-emerald-600 dark:border-slate-700 dark:border-t-emerald-400"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-brain text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-900 dark:text-white">Melatih model Naive Bayes…</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Mengambil dataset, encoding fitur, fitting model, dan menghitung evaluasi.</p>
            </div>
        </div>
    </div>

    {{-- Error Alert --}}
    <div x-show="error" x-transition x-cloak class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
        <i class="fa-solid fa-circle-exclamation text-lg"></i>
        <span class="flex-1 text-sm font-medium" x-text="error"></span>
        <button @click="error = null" class="text-rose-600 hover:text-rose-800 dark:text-rose-300 dark:hover:text-rose-100">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    {{-- Results Section (shown after successful training) --}}
    <template x-if="result">
        <div class="space-y-6" x-transition>

            {{-- Success Banner --}}
            <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                <i class="fa-solid fa-check-circle text-lg"></i>
                <span class="flex-1 text-sm font-medium" x-text="result.message"></span>
            </div>

            {{-- Summary Stat Cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Accuracy --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <i class="fa-solid fa-bullseye text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold text-emerald-600" x-text="((result.accuracy ?? result.train_accuracy ?? result.test_accuracy ?? 0) * 100).toFixed(2) + '%'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Akurasi Training</p>
                        </div>
                    </div>
                </div>
                {{-- CV Accuracy --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <i class="fa-solid fa-arrows-spin text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold text-blue-600" x-text="result.cv_accuracy !== null && result.cv_accuracy !== undefined ? (result.cv_accuracy * 100).toFixed(2) + '%' : 'N/A'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cross-Validation Accuracy</p>
                        </div>
                    </div>
                </div>
                {{-- Total Data --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                            <i class="fa-solid fa-database text-lg"></i>
                        </span>
                        <div>
                            <p class="text-2xl font-bold text-violet-600" x-text="(result.total_data ?? 0) + ' data'"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Sampel Training</p>
                        </div>
                    </div>
                </div>
                {{-- Last Training Date --}}
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                            <i class="fa-solid fa-clock text-lg"></i>
                        </span>
                        <div>
                            <p class="text-lg font-bold text-amber-600" x-text="trainedAt"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Waktu Training Terakhir</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content: 2-column layout --}}
            <div class="grid gap-6 lg:grid-cols-5">

                {{-- Confusion Matrix (3 cols) --}}
                <div class="lg:col-span-3 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-table-cells mr-1.5 text-emerald-600"></i>Confusion Matrix
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Baris = Kelas Aktual, Kolom = Kelas Prediksi</p>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-center text-sm">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-tl-lg"></th>
                                        <template x-for="(label, idx) in result.class_labels" :key="'cm-head-'+idx">
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50"
                                                :class="idx === result.class_labels.length - 1 ? 'rounded-tr-lg' : ''"
                                                x-text="'Pred: ' + label"></th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, rowIdx) in (result.confusion_matrix || result.confusion_matrix_test || [])" :key="'cm-row-'+rowIdx">
                                        <tr class="border-t border-slate-100 dark:border-slate-800">
                                            <td class="px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 text-left whitespace-nowrap"
                                                x-text="'Aktual: ' + result.class_labels[rowIdx]"></td>
                                            <template x-for="(val, colIdx) in row" :key="'cm-cell-'+rowIdx+'-'+colIdx">
                                                <td class="px-4 py-3 font-bold text-base transition-colors"
                                                    :class="rowIdx === colIdx
                                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                                        : (val > 0
                                                            ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400'
                                                            : 'text-slate-400 dark:text-slate-600')"
                                                    x-text="val"></td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Accuracy Donut Chart (2 cols) --}}
                <div class="lg:col-span-2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-chart-pie mr-1.5 text-blue-600"></i>Distribusi Akurasi
                        </h3>
                    </div>
                    <div class="flex items-center justify-center p-6">
                        <div class="relative h-52 w-52">
                            <canvas id="accuracyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Per-class Metrics Table --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-chart-bar mr-1.5 text-violet-600"></i>Metrik Per-Kelas (Precision / Recall / F1-Score)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Kelas</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Precision</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Recall</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">F1-Score</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Support</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(label, idx) in result.class_labels" :key="'metric-'+idx">
                                <tr class="border-t border-slate-100 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                                            <span class="h-2.5 w-2.5 rounded-full"
                                                  :class="{'bg-emerald-500': label === 'Layak', 'bg-amber-500': label === 'Perlu Servis', 'bg-rose-500': label === 'Tidak Layak'}"></span>
                                            <span x-text="label"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                                <div class="h-full rounded-full bg-blue-500 transition-all duration-700"
                                                     :style="'width:' + (getMetric(label, 'precision') * 100) + '%'"></div>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="(getMetric(label, 'precision') * 100).toFixed(1) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                                <div class="h-full rounded-full bg-emerald-500 transition-all duration-700"
                                                     :style="'width:' + (getMetric(label, 'recall') * 100) + '%'"></div>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="(getMetric(label, 'recall') * 100).toFixed(1) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                                <div class="h-full rounded-full bg-violet-500 transition-all duration-700"
                                                     :style="'width:' + (getMetric(label, 'f1-score') * 100) + '%'"></div>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="(getMetric(label, 'f1-score') * 100).toFixed(1) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300" x-text="getMetric(label, 'support')"></td>
                                </tr>
                            </template>

                            {{-- Weighted Average Row --}}
                            <tr class="border-t-2 border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    <i class="fa-solid fa-calculator mr-1.5 text-slate-400"></i>Weighted Avg
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300" x-text="getWeighted('precision')"></td>
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300" x-text="getWeighted('recall')"></td>
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300" x-text="getWeighted('f1-score')"></td>
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300" x-text="getWeighted('support')"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </template>

    {{-- Empty / Initial State --}}
    <div x-show="!result && !loading && !error" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col items-center justify-center gap-4 py-20">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-500/10 dark:to-teal-500/10">
                <i class="fa-solid fa-graduation-cap text-3xl text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-900 dark:text-white">Belum ada hasil training</p>
                <p class="mt-1 max-w-md text-xs text-slate-500 dark:text-slate-400">
                    Klik tombol <strong>"Mulai Training"</strong> di atas untuk melatih model Gaussian Naive Bayes dari dataset yang sudah disiapkan. Hasil evaluasi akan ditampilkan di sini.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function trainingPage() {
    return {
        loading: false,
        result: null,
        error: null,
        trainedAt: null,
        accuracyChartInstance: null,

        async startTraining() {
            this.loading = true;
            this.error = null;
            this.result = null;

            try {
                const response = await fetch('{{ route("prediksi.train") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    this.error = data.message || 'Gagal melatih model. Silakan coba lagi.';
                    return;
                }

                this.result = data;
                this.trainedAt = new Date().toLocaleString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric',
                    hour: '2-digit', minute: '2-digit',
                });

                // Wait for DOM to update, then render chart
                this.$nextTick(() => this.renderAccuracyChart());

            } catch (err) {
                this.error = 'Tidak dapat terhubung ke server. Pastikan Flask API aktif.';
            } finally {
                this.loading = false;
            }
        },

        getMetric(label, metric) {
            if (!this.result) return 0;
            const report = this.result.classification_report || this.result.classification_report_test;
            if (!report) return 0;
            const entry = report[label];
            if (!entry) return 0;
            return entry[metric] ?? 0;
        },

        getWeighted(metric) {
            if (!this.result) return '-';
            const report = this.result.classification_report || this.result.classification_report_test;
            if (!report) return '-';
            const wa = report['weighted avg'];
            if (!wa) return '-';
            const val = wa[metric];
            if (metric === 'support') return val;
            return (val * 100).toFixed(1) + '%';
        },

        renderAccuracyChart() {
            const canvas = document.getElementById('accuracyChart');
            if (!canvas || !this.result) return;

            if (this.accuracyChartInstance) {
                this.accuracyChartInstance.destroy();
            }

            const accuracy = this.result.accuracy ?? this.result.train_accuracy ?? this.result.test_accuracy ?? 0;
            const remainder = Math.max(0, 1 - accuracy);

            // Detect dark mode
            const isDark = document.documentElement.classList.contains('dark');

            this.accuracyChartInstance = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Benar', 'Salah'],
                    datasets: [{
                        data: [accuracy, remainder],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.85)',
                            isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
                        ],
                        borderWidth: 0,
                        borderRadius: 4,
                    }],
                },
                options: {
                    cutout: '72%',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.label + ': ' + (ctx.parsed * 100).toFixed(2) + '%'
                            }
                        },
                    },
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw(chart) {
                        const { ctx, width, height } = chart;
                        ctx.save();
                        const pct = (accuracy * 100).toFixed(1) + '%';
                        ctx.font = 'bold 24px "Figtree", sans-serif';
                        ctx.fillStyle = isDark ? '#f1f5f9' : '#0f172a';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(pct, width / 2, height / 2 - 8);
                        ctx.font = '11px "Figtree", sans-serif';
                        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
                        ctx.fillText('Akurasi', width / 2, height / 2 + 14);
                        ctx.restore();
                    },
                }],
            });
        },
    };
}
</script>
@endpush
