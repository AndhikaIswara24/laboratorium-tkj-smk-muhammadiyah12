@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Enterprise Asset Intelligence</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-950 sm:text-3xl dark:text-white">Dashboard Prediksi Kelayakan Aset</h1>
            <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">Ringkasan inventaris, kondisi fisik, pemeliharaan, efisiensi, dan performa model Naive Bayes untuk laboratorium TKJ.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="btn-secondary"><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button class="btn-secondary"><i class="fa-regular fa-file-excel"></i> Excel</button>
            <button class="btn-primary"><i class="fa-regular fa-file-pdf"></i> PDF</button>
        </div>
    </div>

    @if(session('success') || session('status'))
        <div data-toast class="fixed right-4 top-20 z-50 rounded-lg border border-emerald-200 bg-white p-4 text-sm font-semibold text-emerald-700 shadow-xl dark:border-emerald-500/20 dark:bg-slate-900 dark:text-emerald-300">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') ?? session('status') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.metric-card label="Total Asset" value="1,240" hint="Semua aset terdaftar" icon="fa-boxes-stacked" tone="blue" trend="+8.4%" />
        <x-dashboard.metric-card label="Asset Layak" value="1,089" hint="Probabilitas layak tinggi" icon="fa-circle-check" tone="green" trend="87.8%" />
        <x-dashboard.metric-card label="Asset Tidak Layak" value="151" hint="Butuh tindakan teknisi" icon="fa-triangle-exclamation" tone="red" trend="12.2%" />
        <x-dashboard.metric-card label="Akurasi Model" value="94.8%" hint="Validasi dataset terakhir" icon="fa-brain" tone="violet" trend="+2.1%" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-dashboard.panel title="Grafik Prediksi" subtitle="Perbandingan output layak dan tidak layak per bulan" icon="fa-chart-line" class="xl:col-span-2">
            <div class="h-80"><canvas id="predictionChart"></canvas></div>
        </x-dashboard.panel>

        <x-dashboard.panel title="Kondisi Asset" subtitle="Distribusi kondisi fisik terkini" icon="fa-chart-pie">
            <div class="h-72"><canvas id="conditionChart"></canvas></div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="rounded-lg bg-emerald-50 p-3 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300"><b class="block text-lg">72%</b>Baik</div>
                <div class="rounded-lg bg-amber-50 p-3 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300"><b class="block text-lg">16%</b>Perawatan</div>
                <div class="rounded-lg bg-rose-50 p-3 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300"><b class="block text-lg">12%</b>Rusak</div>
            </div>
        </x-dashboard.panel>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-dashboard.panel title="Aktivitas Terbaru" subtitle="Log operasional sistem dan user" icon="fa-clock-rotate-left">
            <div class="space-y-4">
                @foreach([
                    ['icon' => 'fa-plus', 'title' => 'Aset baru ditambahkan', 'meta' => 'Printer HP LaserJet oleh Admin', 'time' => '2 jam lalu', 'tone' => 'blue'],
                    ['icon' => 'fa-screwdriver-wrench', 'title' => 'Maintenance selesai', 'meta' => 'PC Lab 1 selesai dibersihkan', 'time' => '5 jam lalu', 'tone' => 'green'],
                    ['icon' => 'fa-brain', 'title' => 'Prediksi dijalankan', 'meta' => '24 aset dianalisis ulang', 'time' => 'Kemarin', 'tone' => 'violet'],
                ] as $activity)
                    <div class="flex gap-3 rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-300"><i class="fa-solid {{ $activity['icon'] }}"></i></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $activity['title'] }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $activity['meta'] }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-dashboard.panel>

        <x-dashboard.panel title="Asset Terbaru" subtitle="Inventaris terakhir masuk" icon="fa-box-open" class="xl:col-span-2">
            <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Aset</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3">Prediksi</th>
                            <th class="px-4 py-3">Probabilitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach([
                            ['kode' => 'LAB-PC-024', 'nama' => 'PC Rakitan Core i5', 'lokasi' => 'Lab TKJ 1', 'prediksi' => 'Layak', 'prob' => '96%'],
                            ['kode' => 'PRN-008', 'nama' => 'Printer LaserJet', 'lokasi' => 'Ruang Guru', 'prediksi' => 'Perawatan', 'prob' => '68%'],
                            ['kode' => 'RTR-014', 'nama' => 'Router MikroTik', 'lokasi' => 'Lab Jaringan', 'prediksi' => 'Layak', 'prob' => '91%'],
                        ] as $asset)
                            <tr class="bg-white transition hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800/60">
                                <td class="px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">{{ $asset['kode'] }}</td>
                                <td class="px-4 py-3 text-slate-900 dark:text-white">{{ $asset['nama'] }}</td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $asset['lokasi'] }}</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $asset['prediksi'] }}</span></td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $asset['prob'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-dashboard.panel>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-dashboard.panel title="Reminder Maintenance" subtitle="Aset yang perlu perawatan" icon="fa-calendar-check">
            <div class="space-y-3">
                <div class="skeleton h-4 w-2/3"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-4/5"></div>
            </div>
            <div class="mt-5 space-y-3">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">UPS Server - jatuh tempo 26 Juni 2026</div>
                <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">Projector Lab 2 - perlu inspeksi lampu</div>
            </div>
        </x-dashboard.panel>

        <x-dashboard.panel title="Ranking Efisiensi" subtitle="Aset paling efisien" icon="fa-ranking-star">
            <div class="space-y-3">
                @foreach(['Router Core' => 98, 'PC Lab 1-03' => 94, 'Switch Manageable' => 89] as $name => $score)
                    <div>
                        <div class="mb-1 flex justify-between text-sm"><span class="font-medium text-slate-700 dark:text-slate-200">{{ $name }}</span><span>{{ $score }}%</span></div>
                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-2 rounded-full bg-blue-600" style="width: {{ $score }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </x-dashboard.panel>

        <x-dashboard.panel title="Variabel Eksternal" subtitle="Faktor dominan kelayakan" icon="fa-bullseye">
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
        data: { labels: ['Baik', 'Perawatan', 'Rusak'], datasets: [{ data: [72, 16, 12], backgroundColor: ['#10b981', '#f59e0b', '#e11d48'], borderWidth: 0 }] },
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
