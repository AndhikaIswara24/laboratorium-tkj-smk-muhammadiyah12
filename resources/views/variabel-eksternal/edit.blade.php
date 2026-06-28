@extends('layouts.app')
@section('title', 'Edit Observasi Variabel Eksternal')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('variabel.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-sliders mr-1"></i>Variabel Eksternal
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Edit Observasi</span>
    </nav>

    {{-- Form Card --}}
    <div class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            {{-- Card Header --}}
            <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                        <i class="fa-solid fa-pen-to-square text-lg text-white"></i>
                    </span>
                    <div>
                        <h1 class="text-lg font-bold text-white">Edit Observasi Variabel Eksternal</h1>
                        <p class="text-sm text-blue-100">Sesuaikan kondisi lingkungan, pasokan daya, sparepart, anggaran, dan efek eksternal untuk aset</p>
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

                <form action="{{ route('variabel.update', $variabel->id_eksternal) }}" method="POST" id="formVariabel">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Aset & Tanggal --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">1</span>
                            Informasi Observasi
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Pilih Aset --}}
                            <div class="sm:col-span-2">
                                <label for="id_aset" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-cube mr-1 text-blue-500"></i> Pilih Aset <span class="text-rose-500">*</span>
                                </label>
                                <select id="id_aset" name="id_aset" required
                                        class="form-control w-full py-2.5 @error('id_aset') !border-rose-400 !ring-rose-400 @enderror">
                                    <option value="">-- Pilih Aset --</option>
                                    @foreach($assets as $aset)
                                        <option value="{{ $aset->id_aset }}" @selected(old('id_aset', $variabel->id_aset) == $aset->id_aset)>
                                            {{ $aset->kode_brg }} — {{ $aset->nama_brg }} ({{ $aset->merk_tipe ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Pilih aset yang dinilai faktor variabel eksternalnya.</p>
                            </div>

                            {{-- Tanggal Observasi --}}
                            <div class="sm:col-span-2">
                                <label for="tgl_observasi" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar mr-1 text-blue-500"></i> Tanggal Observasi <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" id="tgl_observasi" name="tgl_observasi" 
                                       value="{{ old('tgl_observasi', $variabel->tgl_observasi ? $variabel->tgl_observasi->format('Y-m-d') : '') }}" required
                                       class="form-control w-full py-2.5 @error('tgl_observasi') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-xs text-slate-400">Tanggal dilaksanakannya pencatatan/observasi faktor luar.</p>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-8 border-slate-200 dark:border-slate-800">

                    {{-- Section 2: Faktor Eksternal --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">2</span>
                            Penilaian Variabel Eksternal
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Lingkungan --}}
                            <div>
                                <label for="lingkungan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-seedling mr-1 text-emerald-500"></i> Lingkungan Lab <span class="text-rose-500">*</span>
                                </label>
                                <select id="lingkungan" name="lingkungan" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Penilaian --</option>
                                    <option value="Baik" @selected(old('lingkungan', $variabel->lingkungan) === 'Baik')>Baik (Bersih, suhu terjaga, sirkulasi lancar)</option>
                                    <option value="Cukup" @selected(old('lingkungan', $variabel->lingkungan) === 'Cukup')>Cukup (Berdebu sedang, suhu ruang wajar)</option>
                                    <option value="Buruk" @selected(old('lingkungan', $variabel->lingkungan) === 'Buruk')>Buruk (Sangat berdebu, lembab, atau suhu panas)</option>
                                </select>
                            </div>

                            {{-- Daya Listrik --}}
                            <div>
                                <label for="daya_listrik" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-bolt mr-1 text-amber-500"></i> Kestabilan Daya Listrik <span class="text-rose-500">*</span>
                                </label>
                                <select id="daya_listrik" name="daya_listrik" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Penilaian --</option>
                                    <option value="Stabil" @selected(old('daya_listrik', $variabel->daya_listrik) === 'Stabil')>Stabil (Goncangan daya minim/tidak ada pemadaman)</option>
                                    <option value="Tidak Stabil" @selected(old('daya_listrik', $variabel->daya_listrik) === 'Tidak Stabil')>Tidak Stabil (Voltase naik turun, jarang padam)</option>
                                    <option value="Sering Padam" @selected(old('daya_listrik', $variabel->daya_listrik) === 'Sering Padam')>Sering Padam (Aliran listrik sering mati mendadak)</option>
                                </select>
                            </div>

                            {{-- Sparepart --}}
                            <div>
                                <label for="sparepart" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-wrench mr-1 text-blue-500"></i> Ketersediaan Sparepart <span class="text-rose-500">*</span>
                                </label>
                                <select id="sparepart" name="sparepart" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Penilaian --</option>
                                    <option value="Tersedia" @selected(old('sparepart', $variabel->sparepart) === 'Tersedia')>Tersedia (Suku cadang mudah diperoleh/banyak stok)</option>
                                    <option value="Terbatas" @selected(old('sparepart', $variabel->sparepart) === 'Terbatas')>Terbatas (Inden/sulit didapat dalam waktu cepat)</option>
                                    <option value="Tidak Ada" @selected(old('sparepart', $variabel->sparepart) === 'Tidak Ada')>Tidak Ada (Langka/tidak diproduksi lagi)</option>
                                </select>
                            </div>

                            {{-- Anggaran --}}
                            <div>
                                <label for="anggaran" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-coins mr-1 text-amber-600"></i> Dukungan Anggaran <span class="text-rose-500">*</span>
                                </label>
                                <select id="anggaran" name="anggaran" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Penilaian --</option>
                                    <option value="Mendukung" @selected(old('anggaran', $variabel->anggaran) === 'Mendukung')>Mendukung (Dana operasional/perawatan lancar)</option>
                                    <option value="Terbatas" @selected(old('anggaran', $variabel->anggaran) === 'Terbatas')>Terbatas (Harus pengajuan bertahap/dana minim)</option>
                                    <option value="Tidak Ada" @selected(old('anggaran', $variabel->anggaran) === 'Tidak Ada')>Tidak Ada (Sama sekali tidak ada dana khusus)</option>
                                </select>
                            </div>

                            {{-- Efek Eksternal --}}
                            <div class="sm:col-span-2">
                                <label for="ext_effect" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-500"></i> Efek Eksternal Keseluruhan (Ext Effect) <span class="text-rose-500">*</span>
                                </label>
                                <select id="ext_effect" name="ext_effect" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Efek --</option>
                                    <option value="Rendah" @selected(old('ext_effect', $variabel->ext_effect) === 'Rendah')>Rendah (Faktor luar hampir tidak mengganggu fungsi aset)</option>
                                    <option value="Sedang" @selected(old('ext_effect', $variabel->ext_effect) === 'Sedang')>Sedang (Faktor luar sesekali memicu kelambatan/kerusakan)</option>
                                    <option value="Tinggi" @selected(old('ext_effect', $variabel->ext_effect) === 'Tinggi')>Tinggi (Faktor luar sangat mengancam kelangsungan hidup aset)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <a href="{{ route('variabel.index') }}" class="btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
