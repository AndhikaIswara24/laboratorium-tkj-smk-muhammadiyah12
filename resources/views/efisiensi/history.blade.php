@extends('layouts.app')
@section('title', 'Riwayat Efisiensi Aset')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('efisiensi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-gauge-high mr-1"></i>Efisiensi
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Riwayat Efisiensi</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-600"></i>Riwayat Efisiensi Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Lacak metrik efisiensi operasional, jam kerja, performa, dan downtime historis untuk aset ini.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('efisiensi.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('efisiensi.create', ['id_aset' => $asset->id_aset]) }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Observasi
            </a>
        </div>
    </div>

    {{-- 24-Hour Visibility Notice --}}
    <div class="flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-800 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
        <i class="fa-solid fa-clock text-lg"></i>
        <span class="flex-1 text-sm font-medium">Halaman ini hanya menampilkan riwayat efisiensi yang dicatat dalam <strong>24 jam terakhir</strong>. Data yang lebih lama tetap tersimpan di database dan dapat diakses melalui halaman daftar utama atau ekspor laporan.</span>
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

    {{-- Asset Detail Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        {{-- Card Header --}}
        <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                    <i class="fa-solid fa-cube text-lg text-white"></i>
                </span>
                <div>
                    <h2 class="text-base font-bold text-white">{{ $asset->nama_brg }}</h2>
                    <p class="text-xs text-blue-100">Kode Aset: <span class="font-mono font-bold">{{ $asset->kode_brg }}</span></p>
                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Detail 1: Merk/Tipe --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Merk / Tipe</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">{{ $asset->merk_tipe ?? '—' }}</p>
                </div>

                {{-- Detail 2: Lokasi --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Lokasi / Lab</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        <i class="fa-solid fa-location-dot mr-1 text-rose-500"></i>{{ $asset->lokasi ?? '—' }}
                    </p>
                </div>

                {{-- Detail 3: Tahun & Usia Pakai --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tahun & Usia Pakai</p>
                    @php
                        $usia = $asset->thn_perolehan ? (int)date('Y') - (int)$asset->thn_perolehan : null;
                    @endphp
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        {{ $asset->thn_perolehan ?? '—' }}
                        @if($usia !== null)
                            <span class="text-xs font-normal text-slate-500">({{ $usia }} tahun)</span>
                        @endif
                    </p>
                </div>

                {{-- Detail 4: Harga Perolehan --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Harga Perolehan</p>
                    <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-200">
                        {{ $asset->harga_perolehan ? 'Rp ' . number_format($asset->harga_perolehan, 0, ',', '.') : '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                {{-- Detail 5: Asal Usul --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Asal Usul / Sumber Dana</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $asset->asal_usul ?? '—' }}</p>
                </div>

                {{-- Detail 6: Spesifikasi --}}
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Spesifikasi Lengkap</p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $asset->spesifikasi ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- History List --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 class="mb-4 text-base font-bold text-slate-900 dark:text-white">
            <i class="fa-solid fa-list-ul mr-2 text-blue-600"></i>Daftar Riwayat Efisiensi Output
        </h3>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/50">
                            <th class="px-4 py-3 font-semibold text-slate-600 dark:text-slate-300">#</th>
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
                                <td class="px-4 py-3 text-slate-500">
                                    {{ ($rows->currentPage()-1) * $rows->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                        <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
                                        {{ $row->tgl_observasi ? $row->tgl_observasi->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300 font-medium">
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
                                            'Lambat' => 'fa-gauge-simple',
                                            'Mati' => 'fa-power-off',
                                            default => 'fa-question'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $performClass }}">
                                        <i class="fa-solid {{ $performIcon }}"></i>
                                        {{ $row->perform ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300 font-semibold">
                                    {{ $row->umur_ekonomis ?? 0 }} <span class="text-xs font-normal text-slate-400">tahun</span>
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
                                                onclick="openDeleteModal({{ $row->id_efisiensi }})" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                            <i class="fa-solid fa-clipboard-question text-2xl text-slate-300 dark:text-slate-600"></i>
                                        </span>
                                        <div>
                                            <p class="font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat observasi efisiensi</p>
                                            <p class="text-sm text-slate-400">Klik "Tambah Observasi" untuk mencatat efisiensi output pertama aset ini.</p>
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
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-3 dark:border-slate-800 dark:bg-slate-800/50 mt-4 rounded-b-xl">
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $rows->firstItem() }} - {{ $rows->lastItem() }} dari {{ $rows->total() }} data
                    </span>
                    {{ $rows->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
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
            Apakah Anda yakin ingin menghapus data observasi efisiensi terpilih untuk aset <strong class="text-slate-900 dark:text-white">{{ $asset->nama_brg }}</strong>?
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
    function openDeleteModal(id) {
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
