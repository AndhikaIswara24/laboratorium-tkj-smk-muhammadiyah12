@extends('layouts.app')
@section('title', 'Edit Aset')
@section('content')

<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('assets.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">
            <i class="fa-solid fa-boxes-stacked mr-1"></i>Data Aset
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
        <span class="font-medium text-slate-700 dark:text-slate-200">Edit Aset</span>
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
                        <h1 class="text-lg font-bold text-white">Edit Data Aset</h1>
                        <p class="text-sm text-blue-100">Sesuaikan informasi inventaris aset laboratorium TKJ Anda</p>
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

                <form action="{{ route('assets.update', $asset->id_aset) }}" method="POST" id="formAset">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Identifikasi Aset --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">1</span>
                            Identifikasi Aset
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Kode Barang --}}
                            <div>
                                <label for="kode_brg" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-barcode mr-1 text-blue-500"></i> Kode Barang <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="kode_brg" name="kode_brg" required maxlength="20"
                                       value="{{ old('kode_brg', $asset->kode_brg) }}" placeholder="Misal: AST-001"
                                       class="form-control w-full py-2.5 @error('kode_brg') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-xs text-slate-400">Kode barang unik untuk pelacakan inventaris.</p>
                            </div>

                            {{-- Nama Barang --}}
                            <div>
                                <label for="nama_brg" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-cube mr-1 text-blue-500"></i> Nama Barang <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="nama_brg" name="nama_brg" required maxlength="100"
                                       value="{{ old('nama_brg', $asset->nama_brg) }}" placeholder="Misal: Printer HP LaserJet"
                                       class="form-control w-full py-2.5 @error('nama_brg') !border-rose-400 !ring-rose-400 @enderror">
                                <p class="mt-1 text-xs text-slate-400">Nama deskriptif barang aset.</p>
                            </div>

                            {{-- Merk / Tipe --}}
                            <div>
                                <label for="merk_tipe" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-tag mr-1 text-blue-500"></i> Merk / Tipe
                                </label>
                                <input type="text" id="merk_tipe" name="merk_tipe" maxlength="80"
                                       value="{{ old('merk_tipe', $asset->merk_tipe) }}" placeholder="Misal: HP P3015"
                                       class="form-control w-full py-2.5">
                            </div>

                            {{-- Lokasi --}}
                            <div>
                                <label for="lokasi" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-location-dot mr-1 text-rose-500"></i> Lokasi Penempatan
                                </label>
                                <input type="text" id="lokasi" name="lokasi" maxlength="60"
                                       value="{{ old('lokasi', $asset->lokasi) }}" placeholder="Misal: Lab TKJ 1"
                                       class="form-control w-full py-2.5">
                            </div>
                        </div>
                    </div>

                    <hr class="mb-8 border-slate-200 dark:border-slate-800">

                    {{-- Section 2: Spesifikasi & Detail Pengadaan --}}
                    <div class="mb-8">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 text-xs text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">2</span>
                            Spesifikasi & Detail Pengadaan
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-3">
                            {{-- Spesifikasi --}}
                            <div class="sm:col-span-3">
                                <label for="spesifikasi" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i> Detail Spesifikasi
                                </label>
                                <textarea id="spesifikasi" name="spesifikasi" rows="3"
                                          placeholder="Tuliskan spesifikasi detail RAM, CPU, kapasitas penyimpanan, kelengkapan port dll..."
                                          class="form-control w-full py-2.5">{{ old('spesifikasi', $asset->spesifikasi) }}</textarea>
                            </div>

                            {{-- Tahun Perolehan --}}
                            <div>
                                <label for="thn_perolehan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar mr-1 text-blue-500"></i> Tahun Perolehan
                                </label>
                                <input type="number" id="thn_perolehan" name="thn_perolehan" min="1900" max="2099"
                                       value="{{ old('thn_perolehan', $asset->thn_perolehan) }}" placeholder="2024"
                                       class="form-control w-full py-2.5">
                            </div>

                            {{-- Harga Perolehan --}}
                            <div>
                                <label for="harga_perolehan" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-coins mr-1 text-emerald-500"></i> Harga Perolehan (Rupiah)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">Rp</span>
                                    <input type="number" id="harga_perolehan" name="harga_perolehan" min="0" step="0.01"
                                           value="{{ old('harga_perolehan', $asset->harga_perolehan) }}" placeholder="0"
                                           class="form-control w-full py-2.5 pl-9">
                                </div>
                            </div>

                            {{-- Asal Usul --}}
                            <div>
                                <label for="asal_usul" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    <i class="fa-solid fa-file-invoice mr-1 text-blue-500"></i> Asal Usul <span class="text-rose-500">*</span>
                                </label>
                                <select id="asal_usul" name="asal_usul" required class="form-control w-full py-2.5">
                                    <option value="">-- Pilih Asal Usul --</option>
                                    @foreach($asalUsul as $option)
                                        <option value="{{ $option }}" @selected(old('asal_usul', $asset->asal_usul) === $option)>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <a href="{{ route('assets.index') }}" class="btn-secondary">
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
