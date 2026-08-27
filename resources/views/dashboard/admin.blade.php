@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Enterprise Asset
                    Intelligence</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-950 sm:text-3xl dark:text-white">Dashboard Prediksi Kelayakan
                    Aset</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">Ringkasan inventaris, kondisi fisik,
                    pemeliharaan, efisiensi, dan performa model Naive Bayes untuk laboratorium TKJ.</p>
            </div>
        </div>

        @if(session('success') || session('status'))
            <div data-toast
                class="fixed right-4 top-20 z-50 rounded-lg border border-emerald-200 bg-white p-4 text-sm font-semibold text-emerald-700 shadow-xl dark:border-emerald-500/20 dark:bg-slate-900 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') ?? session('status') }}
            </div>
        @endif

        {{-- Main Metrics --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-dashboard.metric-card label="Total Asset" value="{{ $totalAssets }}" hint="Semua aset terdaftar"
                icon="fa-boxes-stacked" tone="blue" trend="" />
            <x-dashboard.metric-card label="Asset Layak" value="{{ $distribKondisi['Layak'] }}"
                hint="Probabilitas layak tinggi" icon="fa-circle-check" tone="green" trend="" />
            <x-dashboard.metric-card label="Asset Tidak Layak" value="{{ $distribKondisi['Tidak Layak'] }}"
                hint="Butuh tindakan teknisi" icon="fa-triangle-exclamation" tone="red" trend="" />
            <x-dashboard.metric-card label="Akurasi Model" value="94.8%" hint="Validasi dataset terakhir" icon="fa-brain"
                tone="violet" trend="+2.1%" />
        </div>

        {{-- Historical Table Row Metrics --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-database mr-1"></i> Data Riwayat Historis (Jumlah Baris)
            </h3>
            <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-slate-50 p-4 dark:bg-slate-800/40">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kondisi Fisik</span>
                    <p class="mt-1 text-xl font-bold text-slate-800 dark:text-slate-200">{{ $countKondisi }} <span
                            class="text-xs font-normal text-slate-500">baris</span></p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4 dark:bg-slate-800/40">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pemeliharaan</span>
                    <p class="mt-1 text-xl font-bold text-slate-800 dark:text-slate-200">{{ $countPemeliharaan }} <span
                            class="text-xs font-normal text-slate-500">baris</span></p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4 dark:bg-slate-800/40">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Efisiensi Output</span>
                    <p class="mt-1 text-xl font-bold text-slate-800 dark:text-slate-200">{{ $countEfisiensi }} <span
                            class="text-xs font-normal text-slate-500">baris</span></p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4 dark:bg-slate-800/40">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Variabel Eksternal</span>
                    <p class="mt-1 text-xl font-bold text-slate-800 dark:text-slate-200">{{ $countVariabel }} <span
                            class="text-xs font-normal text-slate-500">baris</span></p>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid gap-6 xl:grid-cols-3">
            <x-dashboard.panel title="Grafik Prediksi" subtitle="Perbandingan output layak dan tidak layak per bulan"
                icon="fa-chart-line" class="xl:col-span-2">
                <div class="h-80"><canvas id="predictionChart"></canvas></div>
            </x-dashboard.panel>

            <x-dashboard.panel title="Distribusi Label Kelas" subtitle="Distribusi kelas kondisi kelayakan saat ini"
                icon="fa-chart-pie">
                <div class="h-72"><canvas id="conditionChart"></canvas></div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <b class="block text-base">{{ $distribKondisi['Layak'] }}</b>Layak
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                        <b class="block text-base">{{ $distribKondisi['Perlu Servis'] }}</b>Servis
                    </div>
                    <div class="rounded-lg bg-rose-50 p-3 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">
                        <b class="block text-base">{{ $distribKondisi['Tidak Layak'] }}</b>Tidak Layak
                    </div>
                </div>
            </x-dashboard.panel>
        </div>

        {{-- Completion Indicators & Data Progress --}}
        <div class="grid gap-6 xl:grid-cols-3">
            <x-dashboard.panel title="Kelengkapan Data per Aset"
                subtitle="Aset yang belum memiliki data lengkap di 4 tabel historis" icon="fa-list-check"
                class="xl:col-span-2">
                <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead
                            class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Aset</th>
                                <th class="px-4 py-3">Progress</th>
                                <th class="px-4 py-3">Kekurangan Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse(collect($incompleteAssets)->take(6) as $item)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">{{ $item['kode_brg'] }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-900 dark:text-white truncate max-w-[150px]">
                                        {{ $item['nama_brg'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-16 rounded-full bg-slate-100 dark:bg-slate-800">
                                                <div class="h-2 rounded-full @if($item['score'] == 3) bg-blue-500 @elseif($item['score'] == 2) bg-amber-500 @else bg-rose-500 @endif"
                                                    style="width: {{ ($item['score'] / 4) * 100 }}%"></div>
                                            </div>
                                            <span
                                                class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ ($item['score'] / 4) * 100 }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item['missing'] as $miss)
                                                <span
                                                    class="rounded bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">
                                                    {{ $miss }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-3xl mb-2"></i>
                                        <p class="text-sm font-semibold">Semua aset telah memiliki data lengkap di 4 tabel
                                            historis!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-dashboard.panel>

            <x-dashboard.panel title="Faktor Eksternal Terbesar" subtitle="Variabel eksternal dengan pengaruh tertinggi"
                icon="fa-bullseye">
                <div class="h-64"><canvas id="radarChart"></canvas></div>
            </x-dashboard.panel>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const chartText = document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569';
                Chart.defaults.color = chartText;
                Chart.defaults.borderColor = 'rgba(148, 163, 184, .25)';

                new Chart(document.getElementById('predictionChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                        datasets: [
                            { label: 'Layak', data: [140, 155, 168, 172, 188, 205], borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, .12)', fill: true, tension: .4 },
                            { label: 'Tidak Layak', data: [32, 28, 35, 31, 24, 29], borderColor: '#e11d48', backgroundColor: 'rgba(225, 29, 72, .1)', fill: true, tension: .4 }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });

                new Chart(document.getElementById('conditionChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Layak', 'Perlu Servis', 'Tidak Layak'],
                        datasets: [{
                            data: [{{ $distribKondisi['Layak'] }}, {{ $distribKondisi['Perlu Servis'] }}, {{ $distribKondisi['Tidak Layak'] }}],
                            backgroundColor: ['#10b981', '#f59e0b', '#e11d48'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom' } } }
                });

                new Chart(document.getElementById('radarChart'), {
                    type: 'radar',
                    data: { labels: ['Suhu', 'Kelembapan', 'Beban Pakai', 'Usia', 'Lokasi'], datasets: [{ label: 'Pengaruh', data: [70, 62, 88, 76, 58], borderColor: '#7c3aed', backgroundColor: 'rgba(124, 58, 237, .18)' }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { beginAtZero: true, max: 100 } } }
                });
            });
        </script>
    @endpush

@endsection