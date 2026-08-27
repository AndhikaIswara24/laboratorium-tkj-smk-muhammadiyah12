@extends('layouts.app')
@section('title', 'Evaluasi Model Naive Bayes')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('prediksi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-brain mr-1"></i>Prediksi Naive Bayes
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Evaluasi Model</span>
    </nav>

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            <i class="fa-solid fa-square-poll-vertical mr-2 text-rose-600"></i>Evaluasi Model Naive Bayes
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Halaman analisis performa model (Read-Only) yang menguji hasil prediksi aktual terhadap label ground truth dari dataset.
        </p>
    </div>

    {{-- Stats Cards Deck --}}
    <div class="grid gap-6 sm:grid-cols-2">
        {{-- Total Evaluated Samples --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-database text-xl"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $total }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Sampel Evaluasi</p>
                </div>
            </div>
        </div>

        {{-- Model Accuracy --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-bullseye text-xl"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ number_format($accuracy * 100, 2) }}%</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Akurasi Pengujian</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Left: Classification Report Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-chart-bar mr-1.5 text-rose-600"></i>Laporan Metrik Klasifikasi (Classification Report)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/50">
                            <th class="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300">Kelas</th>
                            <th class="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300">Precision</th>
                            <th class="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300">Recall</th>
                            <th class="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300">F1-Score</th>
                            <th class="px-6 py-3.5 font-bold text-slate-600 dark:text-slate-300">Support</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($metrics as $class => $m)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full"
                                              :class="{
                                                  'bg-emerald-500': '{{ $class }}' === 'Layak',
                                                  'bg-amber-500': '{{ $class }}' === 'Perlu Servis',
                                                  'bg-rose-500': '{{ $class }}' === 'Tidak Layak'
                                              }"></span>
                                        {{ $class }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ number_format($m['precision'] * 100, 1) }}%
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ number_format($m['recall'] * 100, 1) }}%
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ number_format($m['f1'] * 100, 1) }}%
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ $m['support'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right: Confusion Matrix --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-table-cells mr-1.5 text-rose-600"></i>Confusion Matrix
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Baris = Ground Truth Aktual, Kolom = Kelas Prediksi</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-xs">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-tl-xl"></th>
                                @foreach($classes as $c)
                                    <th class="px-3 py-2.5 bg-slate-50 font-bold text-slate-600 dark:bg-slate-800/50 dark:text-slate-300">Pred: {{ $c }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $actual)
                                <tr class="border-t border-slate-100 dark:border-slate-800">
                                    <td class="px-3 py-3 text-left font-bold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50">Aktual: {{ $actual }}</td>
                                    @foreach($classes as $predicted)
                                        @php
                                            $val = $confusionMatrix[$actual][$predicted];
                                            $isDiagonal = ($actual === $predicted);
                                            $cellClass = 'text-slate-400 dark:text-slate-600';
                                            if ($isDiagonal) {
                                                $cellClass = 'bg-emerald-50 text-emerald-700 font-bold dark:bg-emerald-500/10 dark:text-emerald-400';
                                            } elseif ($val > 0) {
                                                $cellClass = 'bg-rose-50 text-rose-600 font-bold dark:bg-rose-500/10 dark:text-rose-400';
                                            }
                                        @endphp
                                        <td class="px-3 py-3 text-sm {{ $cellClass }}">
                                            {{ $val }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Chart --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Distribution Pie/Doughnut Chart (Takes 1 col) --}}
        <div class="lg:col-span-1 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-chart-pie mr-1.5 text-rose-600"></i>Proporsi Prediksi Kelas
                </h3>
            </div>
            <div class="p-6 flex items-center justify-center">
                <div class="relative h-48 w-48">
                    <canvas id="propChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Distribution Bar Chart (Takes 2 cols) --}}
        <div class="lg:col-span-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/30">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-chart-simple mr-1.5 text-rose-600"></i>Perbandingan Volume Prediksi Kelayakan
                </h3>
            </div>
            <div class="p-6">
                <div class="h-48 w-full relative">
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    
    // Labels and Values
    const labels = {!! json_encode(array_keys($predictionDistribution)) !!};
    const values = {!! json_encode(array_values($predictionDistribution)) !!};

    const colors = [
        'rgba(16, 185, 129, 0.85)', // Layak - Green
        'rgba(245, 158, 11, 0.85)', // Perlu Servis - Amber
        'rgba(239, 68, 68, 0.85)',  // Tidak Layak - Rose
    ];

    // 1. Doughnut Chart
    const propCtx = document.getElementById('propChart').getContext('2d');
    new Chart(propCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 2. Bar Chart
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    new Chart(volumeCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Prediksi',
                data: values,
                backgroundColor: colors,
                borderRadius: 8,
                barThickness: 32,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#475569' }
                },
                y: {
                    grid: { color: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)' },
                    ticks: {
                        color: isDark ? '#94a3b8' : '#475569',
                        stepSize: 1,
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
