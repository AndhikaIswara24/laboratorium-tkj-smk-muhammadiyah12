@extends('layouts.app')
@section('title', 'Prediksi Naive Bayes')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            <i class="fa-solid fa-brain mr-2 text-blue-600"></i>Prediksi Naive Bayes
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Klasifikasi kelayakan aset laboratorium menggunakan algoritma Naive Bayes berdasarkan metrik data historis.
        </p>
    </div>

    {{-- Menu Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- Card 1: Dataset Flat --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-database text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Dataset Flat</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Lihat hasil penggabungan data dari 4 tabel historis menjadi satu dataset flat, serta lakukan regenerasi dataset.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.dataset') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 text-xs w-full justify-center">
                    Kelola Dataset <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 2: Training Model --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fa-solid fa-graduation-cap text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Training Model</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Latih model Naive Bayes menggunakan dataset yang telah disiapkan dan lihat performa model.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.training') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 !bg-emerald-600 hover:!bg-emerald-700 text-xs w-full justify-center">
                    Training Model <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 3: Prediksi Kelayakan Aset --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Prediksi Kelayakan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Lakukan kalkulasi prediksi kelayakan aset secara otomatis berdasarkan data observasi terbaru.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.kelayakan') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 !bg-violet-600 hover:!bg-violet-700 text-xs w-full justify-center">
                    Prediksi Kelayakan <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 4: Ringkasan Prediksi --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                    <i class="fa-solid fa-list-check text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ringkasan Prediksi</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Lihat rekapitulasi prediksi kelayakan terbaru, saring lokasi/label, serta akses riwayat 4 tabel terkait.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.summary') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 !bg-sky-600 hover:!bg-sky-700 text-xs w-full justify-center">
                    Ringkasan Prediksi <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 5: Evaluasi Model --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Evaluasi Model</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Lihat metrik performa model seperti akurasi, precision/recall/F1, dan visualisasi distribusi secara real-time.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.evaluasi') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 !bg-rose-600 hover:!bg-rose-700 text-xs w-full justify-center">
                    Evaluasi Model <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 6: Laporan Kelayakan --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Laporan Kelayakan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed text-xs">
                    Cetak laporan resmi kelayakan aset, filter data, tampilkan total kategori, serta ekspor ke PDF/Excel/CSV.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.laporan_kelayakan') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5 !bg-amber-600 hover:!bg-amber-700 text-xs w-full justify-center">
                    Laporan Kelayakan <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
