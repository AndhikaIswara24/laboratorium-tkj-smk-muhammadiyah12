@php
    $navGroups = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'fa-chart-line'],
        ],
        'Asset Intelligence' => [
            ['label' => 'Data Aset', 'route' => 'assets.index', 'match' => 'assets.*', 'icon' => 'fa-boxes-stacked'],
            ['label' => 'Kondisi Fisik', 'route' => 'kondisi.index', 'match' => 'kondisi.*', 'icon' => 'fa-stethoscope'],
            ['label' => 'Pemeliharaan', 'route' => 'pemeliharaan.index', 'match' => 'pemeliharaan.*', 'icon' => 'fa-screwdriver-wrench'],
            ['label' => 'Efisiensi', 'route' => 'efisiensi.index', 'match' => 'efisiensi.*', 'icon' => 'fa-gauge-high'],
            ['label' => 'Variabel Eksternal', 'route' => 'variabel.index', 'match' => 'variabel.*', 'icon' => 'fa-sliders'],
        ],
        'Analitik' => [
            ['label' => 'Prediksi Naive Bayes', 'route' => 'prediksi.index', 'match' => 'prediksi.*', 'icon' => 'fa-brain'],
            ['label' => 'Laporan', 'route' => 'laporan.index', 'match' => 'laporan.*', 'icon' => 'fa-file-export'],
        ],
    ];
@endphp

<aside id="sidebar" class="fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-72 -translate-x-full border-r border-slate-200 bg-white transition-all duration-300 lg:translate-x-0 dark:border-slate-800 dark:bg-slate-950">
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200 p-4 dark:border-slate-800">
            <div class="rounded-lg bg-slate-100 p-4 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Model Status</p>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold text-slate-950 dark:text-white">94.8%</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Akurasi Naive Bayes</p>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Live</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            @foreach($navGroups as $group => $items)
                <div>
                    <p class="sidebar-label px-3 pb-2 text-xs font-bold uppercase tracking-wider text-slate-400">{{ $group }}</p>
                    <div class="space-y-1">
                        @foreach($items as $item)
                            @if(Route::has($item['route']) && ($item['route'] !== 'variabel.index' || auth()->user()->role === 'admin') && (!in_array($item['route'], ['prediksi.index', 'laporan.index']) || auth()->user()->role === 'admin'))
                                <a href="{{ route($item['route']) }}" class="sidebar-link group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs($item['match']) ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' }}">
                                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                    <span class="sidebar-text">{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if(auth()->check() && auth()->user()->role === 'admin')
                <div>
                    <p class="sidebar-label px-3 pb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Administrasi</p>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center"></i>
                        <span class="sidebar-text">Manajemen User</span>
                    </a>
                </div>
            @endif
        </nav>

        <div class="border-t border-slate-200 p-4 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <p class="sidebar-text">&copy; 2026 SMK Muhammadiyah 12</p>
        </div>
    </div>
</aside>

<div id="sidebarBackdrop" class="fixed inset-0 z-30 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>
