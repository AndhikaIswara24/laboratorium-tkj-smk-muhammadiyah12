@extends('layouts.app')
@section('title','Tambah Observasi Kondisi Fisik')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('kondisi.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-stethoscope mr-1"></i>Kondisi Fisik
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Tambah Observasi</span>
    </nav>

    {{-- Form Card --}}
    <div class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            {{-- Card Header --}}
            <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-5 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i class="fa-solid fa-plus-circle text-lg text-white"></i>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-white">Tambah Data Observasi</h1>
                        <p class="text-sm text-blue-100">Input data historis kondisi fisik dan teknis aset</p>
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

                <form action="{{ route('kondisi.store') }}" method="POST" id="formKondisiFisik">
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
                                        <option value="{{ $aset->id_aset }}" data-thn="{{ $aset->thn_perolehan }}" data-kode="{{ $aset->kode_brg }}" @selected(old('id_aset', request()->query('id_aset')) == $aset->id_aset)>
                                            {{ $aset->kode_brg }} — {{ $aset->nama_brg }} ({{ $aset->merk_tipe ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Pilih aset yang akan diobservasi kondisi fisiknya.</p>
                                @error('id_aset')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Asset Info Card (akan diisi oleh JS) --}}
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
                                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-300">Usia Pakai (Otomatis)</p>
                                            <p id="infoUsia" class="mt-0.5 text-sm font-bold text-blue-900 dark:text-blue-100">
                                                <span id="usiaValue">-</span> <span class="text-xs font-normal">tahun</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Detail Observasi --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-cyan-100 text-xs text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">2</span>
                            Detail Observasi
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- Tanggal Observasi --}}
                            <div>
                                <label for="tgl_observasi" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar mr-1 text-cyan-500"></i> Tanggal Observasi <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" id="tgl_observasi" name="tgl_observasi"
                                       value="{{ old('tgl_observasi', date('Y-m-d')) }}" required
                                       class="form-control w-full py-2.5 @error('tgl_observasi') !border-rose-400 !ring-rose-400 @enderror">
                                @error('tgl_observasi')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kondisi Barang --}}
                            <div>
                                <label for="kondisi_brg" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-heart-pulse mr-1 text-cyan-500"></i> Kondisi Barang <span class="text-rose-500">*</span>
                                </label>
                                <select id="kondisi_brg" name="kondisi_brg" required
                                        class="form-control w-full py-2.5 @error('kondisi_brg') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="B" @selected(old('kondisi_brg') === 'B')>B (Baik)</option>
                                    <option value="RR" @selected(old('kondisi_brg') === 'RR')>RR (Rusak Ringan)</option>
                                    <option value="RB" @selected(old('kondisi_brg') === 'RB')>RB (Rusak Berat)</option>
                                </select>
                                @error('kondisi_brg')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Keterangan Teknis --}}
                            <div>
                                <label for="ket_teknis" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-microchip mr-1 text-cyan-500"></i> Keterangan Teknis <span class="text-rose-500">*</span>
                                </label>
                                <select id="ket_teknis" name="ket_teknis" required
                                        class="form-control w-full py-2.5 @error('ket_teknis') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Normal" @selected(old('ket_teknis') === 'Normal')>Normal</option>
                                    <option value="Lemah" @selected(old('ket_teknis') === 'Lemah')>Lemah</option>
                                    <option value="Lambat" @selected(old('ket_teknis') === 'Lambat')>Lambat</option>
                                    <option value="Mati Total" @selected(old('ket_teknis') === 'Mati Total')>Mati Total</option>
                                </select>
                                @error('ket_teknis')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section: Penilaian --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-100 text-xs text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">3</span>
                            Penilaian
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Usia Pakai (Readonly) --}}
                            <div>
                                <label for="usia_pakai_display" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-hourglass-half mr-1 text-violet-500"></i> Usia Pakai
                                    <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">OTOMATIS</span>
                                </label>
                                <input type="text" id="usia_pakai_display" readonly
                                       value="{{ old('usia_pakai_display', '—') }}"
                                       class="form-control w-full cursor-not-allowed bg-slate-50 py-2.5 text-slate-500 dark:bg-slate-800"
                                       placeholder="Dihitung otomatis dari tahun perolehan">
                                <p class="mt-1 text-xs text-slate-400">Tahun sekarang ({{ date('Y') }}) − tahun perolehan aset</p>
                            </div>

                            {{-- Frekuensi Kerusakan --}}
                            <div>
                                <label for="frq_kerusakan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-chart-bar mr-1 text-violet-500"></i> Frekuensi Kerusakan <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="frq_kerusakan" name="frq_kerusakan"
                                       min="0" step="1" placeholder="0"
                                       value="{{ old('frq_kerusakan', 0) }}" required
                                       class="form-control w-full py-2.5 @error('frq_kerusakan') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-xs text-slate-400">Jumlah total kerusakan yang pernah terjadi.</p>
                                @error('frq_kerusakan')
                                    <p class="mt-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kelas Label --}}
                            <div class="sm:col-span-2">
                                <label for="kelas_label" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-tag mr-1 text-violet-500"></i> Kelas Label <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="relative">
                                        <input type="radio" name="kelas_label" value="Layak" class="peer sr-only" @checked(old('kelas_label') === 'Layak') required>
                                        <div class="cursor-pointer rounded-xl border-2 border-slate-200 p-4 text-center transition peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-lg peer-checked:shadow-emerald-500/10 hover:border-emerald-300 dark:border-slate-700 dark:peer-checked:border-emerald-500 dark:peer-checked:bg-emerald-500/10 dark:hover:border-emerald-500/50">
                                            <i class="fa-solid fa-thumbs-up mb-2 text-2xl text-emerald-500"></i>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Layak</p>
                                            <p class="mt-0.5 text-xs text-slate-400">Aset masih layak</p>
                                        </div>
                                    </label>
                                    <label class="relative">
                                        <input type="radio" name="kelas_label" value="Perlu Servis" class="peer sr-only" @checked(old('kelas_label') === 'Perlu Servis')>
                                        <div class="cursor-pointer rounded-xl border-2 border-slate-200 p-4 text-center transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-lg peer-checked:shadow-amber-500/10 hover:border-amber-300 dark:border-slate-700 dark:peer-checked:border-amber-500 dark:peer-checked:bg-amber-500/10 dark:hover:border-amber-500/50">
                                            <i class="fa-solid fa-screwdriver-wrench mb-2 text-2xl text-amber-500"></i>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Perlu Servis</p>
                                            <p class="mt-0.5 text-xs text-slate-400">Butuh perbaikan</p>
                                        </div>
                                    </label>
                                    <label class="relative">
                                        <input type="radio" name="kelas_label" value="Tidak Layak" class="peer sr-only" @checked(old('kelas_label') === 'Tidak Layak')>
                                        <div class="cursor-pointer rounded-xl border-2 border-slate-200 p-4 text-center transition peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:shadow-lg peer-checked:shadow-rose-500/10 hover:border-rose-300 dark:border-slate-700 dark:peer-checked:border-rose-500 dark:peer-checked:bg-rose-500/10 dark:hover:border-rose-500/50">
                                            <i class="fa-solid fa-ban mb-2 text-2xl text-rose-500"></i>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Tidak Layak</p>
                                            <p class="mt-0.5 text-xs text-slate-400">Harus disingkirkan</p>
                                        </div>
                                    </label>
                                </div>
                                @error('kelas_label')
                                    <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <a href="{{ route('kondisi.index') }}" class="btn-secondary">
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
        const usiaDisplay = document.getElementById('usia_pakai_display');

        if (!idAset) {
            infoCard.classList.add('hidden');
            usiaDisplay.value = '—';
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
                usiaDisplay.value = data.usia_pakai + ' tahun (dari ' + (data.thn_perolehan || '?') + ')';
            })
            .catch(err => {
                console.error('Error fetching asset data:', err);
                // Fallback: use data attribute from option
                const select = document.getElementById('id_aset');
                const option = select.options[select.selectedIndex];
                const thn = option.getAttribute('data-thn');
                const kode = option.getAttribute('data-kode');
                const currentYear = new Date().getFullYear();
                const usia = thn ? currentYear - parseInt(thn) : 0;

                document.getElementById('infoKode').textContent = kode || '-';
                document.getElementById('infoTahun').textContent = thn || 'Tidak diketahui';
                document.getElementById('usiaValue').textContent = usia;
                usiaDisplay.value = usia + ' tahun (dari ' + (thn || '?') + ')';
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
