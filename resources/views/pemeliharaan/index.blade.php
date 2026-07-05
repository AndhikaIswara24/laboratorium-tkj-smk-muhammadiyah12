@extends('layouts.app')
@section('title', 'Pemeliharaan Aset')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-screwdriver-wrench mr-2 text-blue-600"></i>Pemeliharaan Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Catatan historis pemeliharaan preventif dan korektif aset laboratorium.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pemeliharaan.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Pemeliharaan
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
        <form method="GET" action="{{ route('pemeliharaan.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cari Aset</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="search" name="search" placeholder="Kode / Nama aset..." value="{{ old('search', $search ?? '') }}"
                           class="form-control w-full py-2.5 pl-9 pr-3">
                </div>
            </div>
            <div>
                <label for="jenis_pm" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jenis Pemeliharaan</label>
                <select id="jenis_pm" name="jenis_pm" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Preventif" @selected(($filterJenis ?? '') === 'Preventif')>Preventif</option>
                    <option value="Korektif" @selected(($filterJenis ?? '') === 'Korektif')>Korektif</option>
                    <option value="Tidak Ada" @selected(($filterJenis ?? '') === 'Tidak Ada')>Tidak Ada</option>
                </select>
            </div>
            <div>
                <label for="kon_after" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kondisi Akhir</label>
                <select id="kon_after" name="kon_after" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="B" @selected(($filterKondisi ?? '') === 'B')>Baik (B)</option>
                    <option value="RR" @selected(($filterKondisi ?? '') === 'RR')>Rusak Ringan (RR)</option>
                    <option value="RB" @selected(($filterKondisi ?? '') === 'RB')>Rusak Berat (RB)</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-search"></i> Cari
                </button>
                <a href="{{ route('pemeliharaan.index') }}" class="btn-secondary" title="Reset">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    @php
        $allRows = \App\Models\Pemeliharaan::all();
        $totalPM = $allRows->count();
        $totalPreventif = $allRows->where('jenis_pm', 'Preventif')->count();
        $totalKorektif = $allRows->where('jenis_pm', 'Korektif')->count();
        $totalCost = $allRows->sum('biaya_servis');
    @endphp
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-screwdriver-wrench text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalPM }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Pemeliharaan</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalPreventif }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">PM Preventif</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $totalKorektif }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">PM Korektif</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </span>
                <div>
                    <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($totalCost, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Biaya Pemeliharaan</p>
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
                <a href="{{ route('pemeliharaan.history', $aset->id_aset) }}"
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
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Tgl Pemeliharaan</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Jenis</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Interval</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Pelaksana</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Biaya</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Kondisi Akhir</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Keterangan</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 text-slate-500">{{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                @if($row->asset)
                                    <a href="{{ route('pemeliharaan.history', $row->asset->id_aset) }}" class="group block hover:text-blue-600 dark:hover:text-blue-400">
                                        <div>
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/30">{{ $row->asset->kode_brg }}</span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-slate-500 group-hover:text-blue-600 dark:text-slate-400 dark:group-hover:text-blue-400">{{ Str::limit($row->asset->nama_brg, 25) }}</p>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                    <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
                                    {{ $row->tgl_pm ? $row->tgl_pm->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $jenisClass = match($row->jenis_pm) {
                                        'Preventif' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30',
                                        'Korektif' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30',
                                        default => 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-500/10 dark:text-slate-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $jenisClass }}">
                                    {{ $row->jenis_pm }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $row->interval_bulan ?? 0 }}</span>
                                <span class="text-xs text-slate-400"> bulan</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                {{ $row->pelaksana ?? '-' }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                Rp {{ number_format($row->biaya_servis, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $kondisiClass = match($row->kon_after) {
                                        'B' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30',
                                        'RR' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30',
                                        'RB' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30',
                                        default => 'bg-slate-50 text-slate-700 ring-slate-600/20'
                                    };
                                    $kondisiLabel = match($row->kon_after) {
                                        'B' => 'Baik (B)',
                                        'RR' => 'Rusak Ringan (RR)',
                                        'RB' => 'Rusak Berat (RB)',
                                        default => $row->kon_after
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $kondisiClass }}">
                                    {{ $kondisiLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400 max-w-[200px] truncate" title="{{ $row->ket_pm }}">
                                {{ $row->ket_pm ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('pemeliharaan.edit', $row->id_pm) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-400" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-400"
                                            onclick="openDeleteModal({{ $row->id_pm }}, '{{ $row->asset ? addslashes($row->asset->nama_brg) : 'Data' }}')" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                        <i class="fa-solid fa-screwdriver-wrench text-3xl text-slate-300 dark:text-slate-600"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-600 dark:text-slate-300">Belum ada data pemeliharaan</p>
                                        <p class="text-sm text-slate-400">Klik "Tambah Pemeliharaan" untuk menambahkan data baru.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rows->count())
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-3 dark:border-slate-800 dark:bg-slate-800/50">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $rows->firstItem() }} - {{ $rows->lastItem() }} dari {{ $rows->total() }} data
                </span>
                {{ $rows->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm" style="display:none;">
    <div class="w-full max-w-md animate-[fadeInScale_0.2s_ease-out] rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <div class="mb-4 flex items-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Konfirmasi Hapus</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <p class="mb-6 text-sm text-slate-600 dark:text-slate-300">
            Apakah Anda yakin ingin menghapus data pemeliharaan aset <strong id="deleteItemName" class="text-slate-900 dark:text-white"></strong>?
        </p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('head')
<style>
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
    function openDeleteModal(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('deleteForm').action = '/pemeliharaan/' + id;
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // Close modal on backdrop click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endpush

@endsection
