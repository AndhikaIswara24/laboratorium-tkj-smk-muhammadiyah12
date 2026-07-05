@extends('layouts.app')
@section('title', 'Variabel Eksternal')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-sliders mr-2 text-blue-600"></i>Variabel Eksternal Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pemeriksaan dan pencatatan variabel luar (lingkungan, daya listrik, sparepart, anggaran, dan efek eksternal) yang mempengaruhi kelayakan aset.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('variabel.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Observasi Variabel
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

    {{-- Filter & Search Card --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" action="{{ route('variabel.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cari Aset</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="search" name="search" placeholder="Kode / Nama aset..." value="{{ old('search', $search ?? '') }}"
                           class="form-control w-full py-2.5 pl-9 pr-3">
                </div>
            </div>
            <div>
                <label for="lingkungan" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lingkungan</label>
                <select id="lingkungan" name="lingkungan" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Baik" @selected(($filterLingkungan ?? '') === 'Baik')>Baik</option>
                    <option value="Cukup" @selected(($filterLingkungan ?? '') === 'Cukup')>Cukup</option>
                    <option value="Buruk" @selected(($filterLingkungan ?? '') === 'Buruk')>Buruk</option>
                </select>
            </div>
            <div>
                <label for="daya_listrik" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daya Listrik</label>
                <select id="daya_listrik" name="daya_listrik" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Stabil" @selected(($filterDayaListrik ?? '') === 'Stabil')>Stabil</option>
                    <option value="Tidak Stabil" @selected(($filterDayaListrik ?? '') === 'Tidak Stabil')>Tidak Stabil</option>
                    <option value="Sering Padam" @selected(($filterDayaListrik ?? '') === 'Sering Padam')>Sering Padam</option>
                </select>
            </div>
            <div>
                <label for="sparepart" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sparepart</label>
                <select id="sparepart" name="sparepart" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Tersedia" @selected(($filterSparepart ?? '') === 'Tersedia')>Tersedia</option>
                    <option value="Terbatas" @selected(($filterSparepart ?? '') === 'Terbatas')>Terbatas</option>
                    <option value="Tidak Ada" @selected(($filterSparepart ?? '') === 'Tidak Ada')>Tidak Ada</option>
                </select>
            </div>
            <div>
                <label for="anggaran" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Anggaran</label>
                <select id="anggaran" name="anggaran" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Mendukung" @selected(($filterAnggaran ?? '') === 'Mendukung')>Mendukung</option>
                    <option value="Terbatas" @selected(($filterAnggaran ?? '') === 'Terbatas')>Terbatas</option>
                    <option value="Tidak Ada" @selected(($filterAnggaran ?? '') === 'Tidak Ada')>Tidak Ada</option>
                </select>
            </div>
            <div>
                <label for="ext_effect" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Efek Eksternal</label>
                <div class="flex gap-2">
                    <select id="ext_effect" name="ext_effect" class="form-control w-full py-2.5">
                        <option value="">-- Semua --</option>
                        <option value="Rendah" @selected(($filterExtEffect ?? '') === 'Rendah')>Rendah</option>
                        <option value="Sedang" @selected(($filterExtEffect ?? '') === 'Sedang')>Sedang</option>
                        <option value="Tinggi" @selected(($filterExtEffect ?? '') === 'Tinggi')>Tinggi</option>
                    </select>
                    <button type="submit" class="btn-primary px-3" title="Cari">
                        <i class="fa-solid fa-search"></i>
                    </button>
                    <a href="{{ route('variabel.index') }}" class="btn-secondary px-3" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    @php
        $allRows = \App\Models\VariabelEksternal::all();
        $totalObs = $allRows->count();
        $totalTinggi = $allRows->where('ext_effect', 'Tinggi')->count();
        $totalSedang = $allRows->where('ext_effect', 'Sedang')->count();
        $totalRendah = $allRows->where('ext_effect', 'Rendah')->count();
    @endphp
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-gauge-simple-high text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalObs }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Observasi</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-rose-600">{{ $totalTinggi }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efek Eksternal Tinggi</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $totalSedang }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efek Eksternal Sedang</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalRendah }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efek Eksternal Rendah</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Asset History Links --}}
    @if($assets->count())
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-clock-rotate-left mr-1"></i> Riwayat Per Aset
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($assets as $aset)
                <a href="{{ route('variabel.history', $aset->id_aset) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-blue-500 dark:hover:bg-blue-500/10 dark:hover:text-blue-400">
                    <i class="fa-solid fa-cube"></i>
                    {{ $aset->kode_brg }} - {{ Str::limit($aset->nama_brg, 20) }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Aset</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Tgl Observasi</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Lingkungan</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Daya Listrik</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Sparepart</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Anggaran</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Efek Eksternal</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 text-slate-500">{{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                @if($row->asset)
                                    <a href="{{ route('variabel.history', $row->asset->id_aset) }}" class="group block hover:text-blue-600 dark:hover:text-blue-400">
                                        <div>
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/30">{{ $row->asset->kode_brg }}</span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-slate-500 group-hover:text-blue-600 dark:text-slate-400 dark:group-hover:text-blue-400">{{ Str::limit($row->asset->nama_brg, 25) }}</p>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
                                    {{ $row->tgl_observasi ? $row->tgl_observasi->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3">
                                @php
                                    $dayClass = match($row->daya_listrik) {
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
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3 text-center">
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
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-sliders text-4xl text-slate-300 dark:text-slate-700"></i>
                                    <p class="text-sm font-medium">Belum ada data variabel eksternal.</p>
                                    <a href="{{ route('variabel.create') }}" class="btn-primary mt-2">
                                        <i class="fa-solid fa-plus"></i> Tambah Observasi Sekarang
                                    </a>
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
