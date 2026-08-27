@extends('layouts.app')
@section('title', 'Tambah Observasi Efisiensi')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('efisiensi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-gauge-high mr-1"></i>Efisiensi
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Tambah Observasi</span>
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
                        <h1 class="text-lg font-bold text-white">Tambah Observasi Efisiensi</h1>
                        <p class="text-sm text-blue-100">Catat performa, jam operasional, downtime, dan efisiensi output aset</p>
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

                <form action="{{ route('efisiensi.store') }}" method="POST" id="formEfisiensi">
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
                                <p class="mt-1 text-xs text-slate-400">Pilih aset yang diukur efisiensi outputnya.</p>
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
                                            <p id="infoUsia" class="mt-0.5 text-sm font-bold text-blue-900 dark:text-blue-100">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Metrik Efisiensi & Output --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-100 text-xs text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">2</span>
                            Metrik Efisiensi & Output
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- Tanggal Observasi --}}
                            <div>
                                <label for="tgl_observasi" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar mr-1 text-blue-500"></i> Tanggal Observasi <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" id="tgl_observasi" name="tgl_observasi"
                                       value="{{ old('tgl_observasi', date('Y-m-d')) }}" required
                                       class="form-control w-full py-2.5 @error('tgl_observasi') !border-rose-400 !ring-rose-400 @enderror">
                                @error('tgl_observasi')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jam Ops (Desimal) --}}
                            <div>
                                <label for="jam_ops" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-clock mr-1 text-blue-500"></i> Jam Ops / Hari <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.1" id="jam_ops" name="jam_ops"
                                       value="{{ old('jam_ops', 0) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('jam_ops') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-[10px] text-slate-400">Jam operasional aktif per hari (Desimal)</p>
                                @error('jam_ops')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Penggunaan --}}
                            <div>
                                <label for="penggunaan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-chart-line mr-1 text-blue-500"></i> Tingkat Penggunaan <span class="text-rose-500">*</span>
                                </label>
                                <select id="penggunaan" name="penggunaan" required
                                        class="form-control w-full py-2.5 @error('penggunaan') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="Tinggi" @selected(old('penggunaan') === 'Tinggi')>Tinggi</option>
                                    <option value="Sedang" @selected(old('penggunaan') === 'Sedang')>Sedang</option>
                                    <option value="Tidak Pakai" @selected(old('penggunaan') === 'Tidak Pakai')>Tidak Pakai</option>
                                </select>
                                @error('penggunaan')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jml User --}}
                            <div>
                                <label for="jml_user" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-users mr-1 text-blue-500"></i> Jumlah Pengguna <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="jml_user" name="jml_user"
                                       value="{{ old('jml_user', 0) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('jml_user') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-[10px] text-slate-400">Total pengguna unik/bulan</p>
                                @error('jml_user')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Downtime (Desimal) --}}
                            <div>
                                <label for="downtime" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-hourglass-half mr-1 text-blue-500"></i> Downtime (Jam/Bln) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.1" id="downtime" name="downtime"
                                       value="{{ old('downtime', 0) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('downtime') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-[10px] text-slate-400">Durasi tidak dapat digunakan (Desimal)</p>
                                @error('downtime')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Performa --}}
                            <div>
                                <label for="perform" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-gauge mr-1 text-blue-500"></i> Kecepatan / Performa <span class="text-rose-500">*</span>
                                </label>
                                <select id="perform" name="perform" required
                                        class="form-control w-full py-2.5 @error('perform') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Performa --</option>
                                    <option value="Normal" @selected(old('perform') === 'Normal')>Normal</option>
                                    <option value="Lambat" @selected(old('perform') === 'Lambat')>Lambat</option>
                                    <option value="Mati" @selected(old('perform') === 'Mati')>Mati</option>
                                </select>
                                @error('perform')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Umur Ekonomis --}}
                            <div>
                                <label for="umur_ekonomis" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-hourglass-end mr-1 text-blue-500"></i> Umur Ekonomis (Tahun) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="umur_ekonomis" name="umur_ekonomis"
                                       value="{{ old('umur_ekonomis', 5) }}" min="0" required
                                       class="form-control w-full py-2.5 @error('umur_ekonomis') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-[10px] text-slate-400">Bisa default sesuai jenis aset saat aset dipilih</p>
                                @error('umur_ekonomis')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Efisiensi Output --}}
                            <div>
                                <label for="efi_out" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-arrow-up-right-dots mr-1 text-blue-500"></i> Efisiensi Output <span class="text-rose-500">*</span>
                                </label>
                                <select id="efi_out" name="efi_out" required
                                        class="form-control w-full py-2.5 @error('efi_out') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Output --</option>
                                    <option value="Tinggi" @selected(old('efi_out') === 'Tinggi')>Tinggi</option>
                                    <option value="Sedang" @selected(old('efi_out') === 'Sedang')>Sedang</option>
                                    <option value="Rendah" @selected(old('efi_out') === 'Rendah')>Rendah</option>
                                </select>
                                @error('efi_out')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <a href="{{ route('efisiensi.index') }}" class="btn-secondary">
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
        document.getElementById('infoUsia').textContent = 'Memuat...';

        fetch('/kondisi-fisik/asset-data/' + idAset)
            .then(response => response.json())
            .then(data => {
                document.getElementById('infoKode').textContent = data.kode_brg;
                document.getElementById('infoTahun').textContent = data.thn_perolehan || 'Tidak diketahui';
                document.getElementById('infoUsia').textContent = data.usia_pakai + ' tahun';

                // Default umur ekonomis berdasarkan nama aset
                const name = data.nama_brg.toLowerCase();
                let defaultUmur = 5; // default 5 tahun
                if (name.includes('meja') || name.includes('kursi') || name.includes('lemari') || name.includes('rak') || name.includes('mebeler')) {
                    defaultUmur = 10;
                } else if (name.includes('router') || name.includes('switch') || name.includes('hub') || name.includes('mikrotik') || name.includes('cisco') || name.includes('access point')) {
                    defaultUmur = 7;
                } else if (name.includes('komputer') || name.includes('pc') || name.includes('laptop') || name.includes('server')) {
                    defaultUmur = 5;
                }

                const umurInput = document.getElementById('umur_ekonomis');
                // HANYA jika nilainya kosong atau default 0 atau default 5, kita ganti otomatis
                if (!umurInput.value || umurInput.value == 0 || umurInput.value == 5) {
                    umurInput.value = defaultUmur;
                }
            })
            .catch(err => {
                console.error('Error fetching asset data:', err);
                // Fallback using select option data attributes
                const select = document.getElementById('id_aset');
                const option = select.options[select.selectedIndex];
                const kode = option.getAttribute('data-kode');
                
                document.getElementById('infoKode').textContent = kode || '-';
                document.getElementById('infoTahun').textContent = '-';
                document.getElementById('infoUsia').textContent = '-';
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
