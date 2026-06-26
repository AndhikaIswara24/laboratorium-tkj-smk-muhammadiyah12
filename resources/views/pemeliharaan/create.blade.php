@extends('layouts.app')
@section('title', 'Tambah Pemeliharaan Aset')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('pemeliharaan.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-screwdriver-wrench mr-1"></i>Pemeliharaan
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Tambah Pemeliharaan</span>
    </nav>

    {{-- Form Card --}}
    <div class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            {{-- Card Header --}}
            <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i class="fa-solid fa-plus-circle text-lg text-white"></i>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-white">Tambah Data Pemeliharaan</h1>
                        <p class="text-sm text-blue-100">Input catatan tindakan pemeliharaan aset lab</p>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-6">
                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <div class="flex items-center gap-2 text-sm font-semibold text-rose-800 dark:text-rose-200">
                            <i class="fa-solid fa-circle-exclamation"></i> Terdapat Kesalahan!
                        </div>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-700 dark:text-rose-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pemeliharaan.store') }}" method="POST" id="formPemeliharaan">
                    @csrf

                    {{-- Section: Informasi Aset --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">1</span>
                            Informasi Aset
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Pilih Aset --}}
                            <div class="sm:col-span-2">
                                <label for="id_aset" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-cube mr-1 text-blue-500"></i> Pilih Aset <span class="text-rose-500">*</span>
                                </label>
                                <select id="id_aset" name="id_aset" required
                                        class="form-control w-full py-2.5 @error('id_aset') !border-rose-400 !ring-rose-400 @enderror"
                                        onchange="fetchAssetData(this.value)">
                                    <option value="">-- Pilih Aset --</option>
                                    @foreach($assets as $aset)
                                        <option value="{{ $aset->id_aset }}" data-kode="{{ $aset->kode_brg }}" @selected(old('id_aset', request()->query('id_aset')) == $aset->id_aset)>
                                            {{ $aset->kode_brg }} — {{ $aset->nama_brg }} ({{ $aset->merk_tipe ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Pilih aset yang dilakukan tindakan pemeliharaan.</p>
                                @error('id_aset')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Asset Info Card (diisi oleh JS) --}}
                            <div id="assetInfoCard" class="hidden sm:col-span-2">
                                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/20 dark:bg-blue-500/10">
                                    <div class="grid gap-3 sm:grid-cols-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-300">Kode Barang</p>
                                            <p id="infoKode" class="mt-0.5 text-sm font-bold text-blue-900 dark:text-blue-100">-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-300">Tahun Perolehan</p>
                                            <p id="infoTahun" class="mt-0.5 text-sm font-bold text-blue-900 dark:text-blue-100">-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-300">Usia Pakai</p>
                                            <p id="infoUsia" class="mt-0.5 text-sm font-bold text-blue-900 dark:text-blue-100">
                                                <span id="usiaValue">-</span> <span class="text-xs font-normal">tahun</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Detail Pemeliharaan --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-100 text-xs text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">2</span>
                            Detail Pemeliharaan
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- Tanggal Pemeliharaan --}}
                            <div>
                                <label for="tgl_pm" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar mr-1 text-blue-500"></i> Tanggal PM <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" id="tgl_pm" name="tgl_pm"
                                       value="{{ old('tgl_pm', date('Y-m-d')) }}" required
                                       class="form-control w-full py-2.5 @error('tgl_pm') !border-rose-400 !ring-rose-400 @enderror">
                                @error('tgl_pm')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jenis PM --}}
                            <div>
                                <label for="jenis_pm" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-list-check mr-1 text-blue-500"></i> Jenis PM <span class="text-rose-500">*</span>
                                </label>
                                <select id="jenis_pm" name="jenis_pm" required
                                        class="form-control w-full py-2.5 @error('jenis_pm') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Preventif" @selected(old('jenis_pm') === 'Preventif')>Preventif</option>
                                    <option value="Korektif" @selected(old('jenis_pm') === 'Korektif')>Korektif</option>
                                    <option value="Tidak Ada" @selected(old('jenis_pm') === 'Tidak Ada')>Tidak Ada</option>
                                </select>
                                @error('jenis_pm')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Interval Bulan --}}
                            <div>
                                <label for="interval_bulan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-arrows-spin mr-1 text-blue-500"></i> Interval (Bulan) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="interval_bulan" name="interval_bulan"
                                       value="{{ old('interval_bulan', 0) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('interval_bulan') !border-rose-400 !ring-rose-400 @enderror">
                                @error('interval_bulan')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pelaksana --}}
                            <div>
                                <label for="pelaksana" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-user-gear mr-1 text-blue-500"></i> Pelaksana <span class="text-rose-500">*</span>
                                </label>
                                <select id="pelaksana" name="pelaksana" required
                                        class="form-control w-full py-2.5 @error('pelaksana') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Pelaksana --</option>
                                    <option value="Teknisi Internal" @selected(old('pelaksana') === 'Teknisi Internal')>Teknisi Internal</option>
                                    <option value="Vendor Luar" @selected(old('pelaksana') === 'Vendor Luar')>Vendor Luar</option>
                                    <option value="Guru TKJ" @selected(old('pelaksana') === 'Guru TKJ')>Guru TKJ</option>
                                </select>
                                @error('pelaksana')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Biaya Servis --}}
                            <div>
                                <label for="biaya_servis" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-rupiah-sign mr-1 text-blue-500"></i> Biaya Servis (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="biaya_servis" name="biaya_servis"
                                       value="{{ old('biaya_servis', 0) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('biaya_servis') !border-rose-400 !ring-rose-400 @enderror">
                                @error('biaya_servis')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kondisi After --}}
                            <div>
                                <label for="kon_after" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-heart-circle-check mr-1 text-blue-500"></i> Kondisi Akhir <span class="text-rose-500">*</span>
                                </label>
                                <select id="kon_after" name="kon_after" required
                                        class="form-control w-full py-2.5 @error('kon_after') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="B" @selected(old('kon_after') === 'B')>B (Baik)</option>
                                    <option value="RR" @selected(old('kon_after') === 'RR')>RR (Rusak Ringan)</option>
                                    <option value="RB" @selected(old('kon_after') === 'RB')>RB (Rusak Berat)</option>
                                </select>
                                @error('kon_after')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Keterangan PM --}}
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label for="ket_pm" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-comment-dots mr-1 text-blue-500"></i> Keterangan Pemeliharaan
                                </label>
                                <textarea id="ket_pm" name="ket_pm" rows="3"
                                          placeholder="Catatan pengerjaan servis, penggantian sparepart, dll..."
                                          class="form-control w-full py-2.5 @error('ket_pm') !border-rose-400 !ring-rose-400 @enderror">{{ old('ket_pm') }}</textarea>
                                @error('ket_pm')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <a href="{{ route('pemeliharaan.index') }}" class="btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                        <button type="reset" class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fetchAssetData(idAset) {
        const infoCard = document.getElementById('assetInfoCard');

        if (!idAset) {
            infoCard.classList.add('hidden');
            return;
        }

        // Show loading state
        infoCard.classList.remove('hidden');
        document.getElementById('infoKode').textContent = 'Memuat...';
        document.getElementById('infoTahun').textContent = 'Memuat...';
        document.getElementById('usiaValue').textContent = '...';

        fetch('/kondisi-fisik/asset-data/' + idAset)
            .then(response => response.json())
            .then(data => {
                document.getElementById('infoKode').textContent = data.kode_brg;
                document.getElementById('infoTahun').textContent = data.thn_perolehan || 'Tidak diketahui';
                document.getElementById('usiaValue').textContent = data.usia_pakai;
            })
            .catch(err => {
                console.error('Error fetching asset data:', err);
                // Fallback using select option data attributes
                const select = document.getElementById('id_aset');
                const option = select.options[select.selectedIndex];
                const kode = option.getAttribute('data-kode');
                
                document.getElementById('infoKode').textContent = kode || '-';
                document.getElementById('infoTahun').textContent = '-';
                document.getElementById('usiaValue').textContent = '-';
            });
    }

    // Auto-fill if old value exists
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('id_aset');
        if (select.value) {
            fetchAssetData(select.value);
        }
    });
</script>
@endpush

@endsection
