@extends('layouts.app')
@section('title', 'Laporan Inventaris')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <i class="fa-solid fa-file-export mr-2 text-blue-600"></i>Laporan Inventaris Aset
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Generate, cetak, dan unduh data laporan historis inventaris laboratorium TKJ SMK Muhammadiyah 12.
            </p>
        </div>
    </div>

    {{-- Report Grid Options --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Card: Generate Laporan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                <i class="fa-solid fa-gears text-blue-600 mr-1"></i> Pengaturan Laporan
            </h2>

            <form action="{{ route('laporan.generate') }}" method="GET" target="_blank" class="space-y-5">
                {{-- Pilih Tipe Laporan --}}
                <div>
                    <label for="tipe" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Tipe Laporan <span class="text-rose-500">*</span></label>
                    <select id="tipe" name="tipe" required class="form-control w-full py-2.5">
                        <option value="kondisi">Laporan Kondisi Fisik & Teknis Aset</option>
                        <option value="pemeliharaan">Laporan Pemeliharaan & Servis Aset</option>
                        <option value="efisiensi">Laporan Efisiensi Penggunaan Aset</option>
                        <option value="variabel">Laporan Variabel Eksternal & Lingkungan Aset</option>
                    </select>
                </div>

                {{-- Rentang Tanggal --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="start_date" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Mulai Tanggal</label>
                        <input type="date" id="start_date" name="start_date" class="form-control w-full py-2.5">
                    </div>
                    <div>
                        <label for="end_date" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" class="form-control w-full py-2.5">
                    </div>
                </div>

                {{-- Opsi Aksi --}}
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Pilih Output Laporan <span class="text-rose-500">*</span></label>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <label class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-200 p-3 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-print text-blue-600"></i> Cetak / Print
                            </span>
                            <input type="radio" name="action" value="print" checked class="text-blue-600 focus:ring-blue-500">
                        </label>

                        <label class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-200 p-3 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-regular fa-file-excel text-green-600"></i> Excel (.xls)
                            </span>
                            <input type="radio" name="action" value="excel" class="text-green-600 focus:ring-green-500">
                        </label>

                        <label class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-200 p-3 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                            <span class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-file-csv text-emerald-600"></i> CSV
                            </span>
                            <input type="radio" name="action" value="csv" class="text-emerald-600 focus:ring-emerald-500">
                        </label>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3">
                        <i class="fa-solid fa-file-import mr-1"></i> Proses & Unduh Laporan
                    </button>
                </div>
            </form>
        </div>

        {{-- Card: Panduan & Info --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">
                    <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i> Petunjuk Penggunaan Laporan
                </h2>
                <div class="space-y-3.5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    <p>
                        Menu laporan ini digunakan untuk memformat ulang data historis yang telah diinput menjadi format resmi untuk pelaporan sekolah.
                    </p>
                    <ul class="list-inside list-disc space-y-2">
                        <li><strong>Cetak / Print:</strong> Membuka tab baru dengan format dokumen resmi lengkap dengan Kop Surat Sekolah dan area tanda tangan (Tempat, Tanggal, Nama Pelaksana/Kepala Lab) yang siap dicetak langsung menggunakan printer atau disimpan sebagai PDF.</li>
                        <li><strong>Excel / CSV:</strong> Mengunduh data historis lengkap dalam bentuk lembar kerja Excel atau CSV dengan penyesuaian penulisan nominal mata uang Rupiah secara penuh.</li>
                        <li><strong>Rentang Tanggal:</strong> Kosongkan filter tanggal jika Anda ingin mengunduh seluruh data riwayat dari awal observasi.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-blue-50/50 p-4 border border-blue-100 dark:bg-blue-950/20 dark:border-blue-900/50 text-xs text-blue-800 dark:text-blue-300">
                <i class="fa-solid fa-clock mr-1"></i> Waktu Server Saat Ini: <strong>{{ date('d-m-Y H:i') }} WIB</strong>
            </div>
        </div>
    </div>
</div>

@endsection
