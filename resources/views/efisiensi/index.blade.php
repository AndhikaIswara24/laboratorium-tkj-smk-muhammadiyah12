@extends('layouts.app')
@section('title', 'Efisiensi Output Aset')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-gauge-high mr-2 text-blue-600"></i>Efisiensi Output Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pemeriksaan dan pencatatan metrik efisiensi penggunaan serta output operasional aset.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('efisiensi.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Observasi Efisiensi
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
        <form method="GET" action="{{ route('efisiensi.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cari Aset</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="search" name="search" placeholder="Kode / Nama aset..." value="{{ old('search', $search ?? '') }}"
                           class="form-control w-full py-2.5 pl-9 pr-3">
                </div>
            </div>
            <div>
                <label for="penggunaan" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Penggunaan</label>
                <select id="penggunaan" name="penggunaan" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Tinggi" @selected(($filterPenggunaan ?? '') === 'Tinggi')>Tinggi</option>
                    <option value="Sedang" @selected(($filterPenggunaan ?? '') === 'Sedang')>Sedang</option>
                    <option value="Tidak Pakai" @selected(($filterPenggunaan ?? '') === 'Tidak Pakai')>Tidak Pakai</option>
                </select>
            </div>
            <div>
                <label for="perform" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Performa</label>
                <select id="perform" name="perform" class="form-control w-full py-2.5">
                    <option value="">-- Semua --</option>
                    <option value="Normal" @selected(($filterPerform ?? '') === 'Normal')>Normal</option>
                    <option value="Lambat" @selected(($filterPerform ?? '') === 'Lambat')>Lambat</option>
                    <option value="Mati" @selected(($filterPerform ?? '') === 'Mati')>Mati</option>
                </select>
            </div>
            <div>
                <label for="efi_out" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Efisiensi Output</label>
                <div class="flex gap-2">
                    <select id="efi_out" name="efi_out" class="form-control w-full py-2.5">
                        <option value="">-- Semua --</option>
                        <option value="Tinggi" @selected(($filterEfiOut ?? '') === 'Tinggi')>Tinggi</option>
                        <option value="Sedang" @selected(($filterEfiOut ?? '') === 'Sedang')>Sedang</option>
                        <option value="Rendah" @selected(($filterEfiOut ?? '') === 'Rendah')>Rendah</option>
                    </select>
                    <button type="submit" class="btn-primary" title="Cari">
                        <i class="fa-solid fa-search"></i>
                    </button>
                    <a href="{{ route('efisiensi.index') }}" class="btn-secondary" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    @php
        $allRows = \App\Models\Efisiensi::all();
        $totalObs = $allRows->count();
        $totalTinggi = $allRows->where('efi_out', 'Tinggi')->count();
        $totalSedang = $allRows->where('efi_out', 'Sedang')->count();
        $totalRendah = $allRows->where('efi_out', 'Rendah')->count();
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
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-circle-arrow-up text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalTinggi }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efisiensi Tinggi</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-circle-arrow-right text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $totalSedang }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efisiensi Sedang</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-circle-arrow-down text-lg"></i>
                </span>
                <div>
                    <p class="text-2xl font-bold text-rose-600">{{ $totalRendah }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Efisiensi Rendah</p>
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
                <a href="{{ route('efisiensi.history', $aset->id_aset) }}"
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
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Jam Ops / Bulan</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Penggunaan</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Jml User</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Downtime</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Performa</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Umur Ekonomis</th>
                        <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">Efisiensi Output</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $row)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 text-slate-500">{{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                @if($row->asset)
                                    <a href="{{ route('efisiensi.history', $row->asset->id_aset) }}" class="group block hover:text-blue-600 dark:hover:text-blue-400">
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
                                    {{ $row->tgl_observasi ? $row->tgl_observasi->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                <span class="font-bold">{{ $row->jam_ops }}</span>
                                <span class="text-xs text-slate-400"> jam</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $penggunaanClass = match($row->penggunaan) {
                                        'Tinggi' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Sedang' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
                                        default => 'bg-slate-50 text-slate-700 ring-slate-600/20'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $penggunaanClass }}">
                                    {{ $row->penggunaan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                <span class="font-semibold">{{ $row->jml_user }}</span>
                                <span class="text-xs text-slate-400"> user</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                <span class="font-semibold text-rose-600 dark:text-rose-400">{{ $row->downtime }}</span>
                                <span class="text-xs text-slate-400"> jam/bln</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $performClass = match($row->perform) {
                                        'Normal' => 'text-emerald-600 dark:text-emerald-400',
                                        'Lambat' => 'text-amber-600 dark:text-amber-400',
                                        'Mati' => 'text-rose-600 dark:text-rose-400',
                                        default => 'text-slate-500'
                                    };
                                    $performIcon = match($row->perform) {
                                        'Normal' => 'fa-circle-check',
                                        'Lambat' => 'fa-gauge-simple-min',
                                        'Mati' => 'fa-power-off',
                                        default => 'fa-question'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $performClass }}">
                                    <i class="fa-solid {{ $performIcon }}"></i>
                                    {{ $row->perform ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                <span class="font-bold">{{ $row->umur_ekonomis ?? 0 }}</span>
                                <span class="text-xs text-slate-400"> tahun</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $efiClass = match($row->efi_out) {
                                        'Tinggi' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'Sedang' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-300',
                                        'Rendah' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-300',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $efiClass }}">
                                    {{ $row->efi_out }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('efisiensi.edit', $row->id_efisiensi) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-400" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-400"
                                            onclick="openDeleteModal({{ $row->id_efisiensi }}, '{{ $row->asset ? addslashes($row->asset->nama_brg) : 'Data' }}')" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                        <i class="fa-solid fa-gauge-high text-3xl text-slate-300 dark:text-slate-600"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-600 dark:text-slate-300">Belum ada data observasi efisiensi</p>
                                        <p class="text-sm text-slate-400">Klik "Tambah Observasi Efisiensi" untuk menambahkan data baru.</p>
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
            Apakah Anda yakin ingin menghapus data observasi efisiensi aset <strong id="deleteItemName" class="text-slate-900 dark:text-white"></strong>?
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
        document.getElementById('deleteForm').action = '/efisiensi/' + id;
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
